#!/usr/bin/env python3
"""Genera las fotos de la tienda con Gemini (modelo de imagen «nano banana») a partir de contenido/imagenes.json.

Uso:  python3 scripts/imagenes.py            # genera las que falten (la clave, GEMINI_API_KEY, en .env)
      python3 scripts/imagenes.py kenia.jpg  # rehace una

Cada imagen puede llevar `referencia`: otra imagen ya generada que el modelo usa como modelo (mismo diseño
de bolsa, misma luz). Lo que devuelve el modelo se guarda en bruto en assets/img/bruto/ y se recorta y
redimensiona a su tamaño final con ffmpeg. Todas son ilustraciones generadas con IA: la tienda lo dice.
"""
import base64, json, os, subprocess, sys, urllib.request

RAIZ = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
MODELO = os.environ.get('MODELO_IMAGEN', 'gemini-nano-banana-2.1')
TAMANOS = {'16:9': (2400, 1350), '4:5': (1200, 1500), '1:1': (1400, 1400)}
CARPETAS = {'tema': 'tema/assets/img', 'productos': 'assets/img'}

def generar(prompt, aspecto, referencias):
    partes = [{'inlineData': {'mimeType': 'image/jpeg', 'data': base64.b64encode(open(r, 'rb').read()).decode()}} for r in referencias]
    cuerpo = {'contents': [{'parts': partes + [{'text': prompt}]}],
              'generationConfig': {'responseModalities': ['IMAGE'], 'imageConfig': {'aspectRatio': aspecto, 'imageSize': '2K'}}}
    req = urllib.request.Request(f'https://generativelanguage.googleapis.com/v1beta/models/{MODELO}:generateContent',
                                 data=json.dumps(cuerpo).encode(), method='POST',
                                 headers={'content-type': 'application/json', 'x-goog-api-key': os.environ['GEMINI_API_KEY']})
    d = json.load(urllib.request.urlopen(req, timeout=300))
    for p in d['candidates'][0]['content']['parts']:
        if 'inlineData' in p:
            return base64.b64decode(p['inlineData']['data'])
    raise SystemExit(f'El modelo no devolvió imagen: {json.dumps(d)[:300]}')

def clave():
    """GEMINI_API_KEY del entorno o, si no está, del .env del proyecto."""
    if os.environ.get('GEMINI_API_KEY'):
        return os.environ['GEMINI_API_KEY']
    for linea in open(os.path.join(RAIZ, '.env')):
        if linea.startswith('GEMINI_API_KEY='):
            return linea.split('=', 1)[1].strip().strip('"')
    raise SystemExit('Falta GEMINI_API_KEY (en el entorno o en .env)')

def main():
    os.environ['GEMINI_API_KEY'] = clave()
    cfg = json.load(open(os.path.join(RAIZ, 'contenido/imagenes.json')))
    estilo = cfg['estilo']
    solo = sys.argv[1:]
    os.makedirs(os.path.join(RAIZ, 'assets/img/bruto'), exist_ok=True)
    for im in cfg['imagenes']:
        destino = os.path.join(RAIZ, CARPETAS[im['donde']], im['archivo'])
        if (solo and im['archivo'] not in solo) or (not solo and os.path.exists(destino)):
            continue
        refs = [os.path.join(RAIZ, CARPETAS[cfg_ref['donde']], cfg_ref['archivo'])
                for cfg_ref in cfg['imagenes'] if cfg_ref['archivo'] in im.get('referencias', [])]
        print(f'🎨 {im["archivo"]:<20} {im["aspecto"]}{"  (con referencia)" if refs else ""}', flush=True)
        bruto = os.path.join(RAIZ, 'assets/img/bruto', im['archivo'])
        open(bruto, 'wb').write(generar(f'{im["prompt"]}\n\n{estilo}', im['aspecto'], refs))
        w, h = TAMANOS[im['aspecto']]
        subprocess.run(['ffmpeg', '-v', 'error', '-y', '-i', bruto, '-vf',
                        f"scale={w}:{h}:force_original_aspect_ratio=increase:flags=lanczos,crop={w}:{h}", '-q:v', '3', destino], check=True)
    print('Listo: las fotos están en assets/img y tema/assets/img')

if __name__ == '__main__':
    main()
