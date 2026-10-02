import sys
from curl_cffi import requests

u = 'https://nexuspharma.to/wp-content/uploads/2025/12/HCG-1-694x1024.png'
try:
    r = requests.get(u, impersonate='chrome', headers={'Referer': 'https://nexuspharma.to/'}, timeout=60)
    print('status', r.status_code, '| ctype', r.headers.get('content-type'), '| len', len(r.content))
    if r.status_code == 200 and 'image' in (r.headers.get('content-type') or ''):
        open(r'C:\Users\HP\Herd\vitalis\storage\downloads\test-py.png', 'wb').write(r.content)
        print('SAVED test-py.png')
except Exception as e:
    print('ERROR', type(e).__name__, e)
