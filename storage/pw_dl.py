import os
from playwright.sync_api import sync_playwright

OUT = r'C:\Users\HP\Herd\vitalis\storage\downloads'
os.makedirs(OUT, exist_ok=True)
LOG = os.path.join(OUT, 'pw.log')
logf = open(LOG, 'a', encoding='utf-8')

def log(m):
    logf.write(str(m) + '\n')
    logf.flush()

urls = [
 'https://nexuspharma.to/wp-content/uploads/2025/12/nexus-pharma-transparent-3-scaled-e1764957533768.png',
 'https://nexuspharma.to/wp-content/uploads/2025/12/nexus-pharma-transparent-2-scaled-e1764957830166.png',
 'https://nexuspharma.to/wp-content/uploads/2025/12/MOCKUP-Primo-1-600x600.png',
 'https://nexuspharma.to/wp-content/uploads/2026/01/Test-Report-97109-300x300.png',
 'https://nexuspharma.to/wp-content/uploads/2025/12/MOCKUP-Proviron-600x600.png',
 'https://nexuspharma.to/wp-content/uploads/2026/02/previon-25-1-300x300.jpg',
 'https://nexuspharma.to/wp-content/uploads/2025/12/MOCKUP-Sustanon-2-600x600.png',
 'https://nexuspharma.to/wp-content/uploads/2026/01/Test-Report-97113-300x300.png',
 'https://nexuspharma.to/wp-content/uploads/2025/12/MOCKUP-Tamoxifen-600x600.png',
 'https://nexuspharma.to/wp-content/uploads/2026/02/tamoxifen-20-1-300x300.jpg',
 'https://nexuspharma.to/wp-content/uploads/2025/12/MOCKUP-Test-Cyp-1-600x600.png',
 'https://nexuspharma.to/wp-content/uploads/2026/04/Test-Report-111771-300x300.png',
 'https://nexuspharma.to/wp-content/uploads/2026/09/MOCKUP-Test-Ent-600x600.jpg',
 'https://nexuspharma.to/wp-content/uploads/2026/09/test_E_250-300x300.png',
 'https://nexuspharma.to/wp-content/uploads/2025/12/Test-E-300-600x600.png',
 'https://nexuspharma.to/wp-content/uploads/2026/01/Test-Report-97107-300x300.png',
 'https://nexuspharma.to/wp-content/uploads/2025/12/MOCKUP-Test-Prop-2-600x600.png',
 'https://nexuspharma.to/wp-content/uploads/2026/01/Test-Report-97110-300x300.png',
 'https://nexuspharma.to/wp-content/uploads/2025/12/MOCKUP-Tren-Ace-1-600x600.png',
 'https://nexuspharma.to/wp-content/uploads/2026/01/Test-Report-97102-300x300.png',
 'https://nexuspharma.to/wp-content/uploads/2025/12/MOCKUP-Tren-Ent-1-600x600.png',
 'https://nexuspharma.to/wp-content/uploads/2026/01/Test-Report-97114-300x300.png',
 'https://nexuspharma.to/wp-content/uploads/2025/12/MOCKUP-Stanozolol-600x600.png',
 'https://nexuspharma.to/wp-content/uploads/2026/02/stanozolol-50-1-300x300.jpg',
 'https://nexuspharma.to/wp-content/themes/motta/images/empty-bag.svg',
]
urls += [
 'https://nexuspharma.to/wp-content/uploads/elementor/thumbs/nexus-pharma-transparent-rfpy6j6760udcf8ua05u25naghhm88gqzv204xgte8.png',
 'https://nexuspharma.to/wp-content/uploads/2026/03/photo_2026-03-23_12-21-49-768x845.jpg',
 'https://nexuspharma.to/wp-content/uploads/2025/12/nexus-labs-white-1-scaled-e1764932786804-768x365.png',
 'https://nexuspharma.to/wp-content/uploads/2025/12/trenbolone-e-200-2-1-694x1024.png',
 'https://nexuspharma.to/wp-content/uploads/2025/12/trenbolone-a-100-2-1-694x1024.png',
 'https://nexuspharma.to/wp-content/uploads/2025/12/testosterone-c-250-2-1-694x1024.png',
 'https://nexuspharma.to/wp-content/uploads/2025/12/primobolan-e-100-2-1-694x1024.png',
 'https://nexuspharma.to/wp-content/uploads/2025/12/masteron-e-200-3-1-694x1024.png',
 'https://nexuspharma.to/wp-content/uploads/2025/12/masteron-e-200-2-1-694x1024.png',
 'https://nexuspharma.to/wp-content/uploads/2025/12/HCG-1-694x1024.png',
 'https://nexuspharma.to/wp-content/uploads/2025/12/GLOW-1-694x1024.png',
 'https://nexuspharma.to/wp-content/uploads/2025/12/BPC_TB-1-694x1024.png',
 'https://nexuspharma.to/wp-content/uploads/2025/12/web-image-scaled.png',
 'https://nexuspharma.to/wp-content/plugins/motta-addons/assets/images/person.jpg',
]

api_targets = [
 ('https://nexuspharma.to/wp-json/wc/store/v1/products?per_page=100', 'wc-products.json'),
 ('https://nexuspharma.to/shop/', 'site-shop.html'),
]

UA = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36'

def run(headless):
    with sync_playwright() as pw:
        args = ['--disable-blink-features=AutomationControlled', '--window-position=-2400,-2400', '--window-size=1280,900']
        browser = pw.chromium.launch(channel='msedge', headless=headless, args=args)
        ctx = browser.new_context(user_agent=UA, locale='en-US', viewport={'width': 1280, 'height': 900})
        page = ctx.new_page()
        try:
            page.goto('https://nexuspharma.to/', wait_until='domcontentloaded', timeout=60000)
        except Exception as e:
            log('goto err ' + str(e)[:120])
        ok = False
        for _ in range(25):
            try:
                if 'Just a moment' not in page.title():
                    ok = True
                    break
            except Exception:
                pass
            page.wait_for_timeout(1000)
        log('HEADLESS' if headless else 'HEADED')
        log('challenge passed' if ok else 'challenge NOT passed')
        if not ok:
            browser.close()
            return False
        for u, name in api_targets:
            try:
                r = ctx.request.get(u, timeout=60000, headers={'Referer': 'https://nexuspharma.to/'})
                body = r.body()
                open(os.path.join(OUT, name), 'wb').write(body)
                log('OK   ' + name + ' status=' + str(r.status) + ' len=' + str(len(body)))
            except Exception as e:
                log('FAIL ' + name + ' ' + type(e).__name__ + ' ' + str(e)[:120])
        for u in urls:
            n = u.rstrip('/').split('/')[-1]
            try:
                r = ctx.request.get(u, timeout=60000, headers={'Referer': 'https://nexuspharma.to/'})
                body = r.body()
                ct = r.headers.get('content-type', '')
                if r.status == 200 and body and 'image' in ct:
                    open(os.path.join(OUT, n), 'wb').write(body)
                    log('OK   ' + n + ' ' + str(len(body)))
                else:
                    log('FAIL ' + n + ' status=' + str(r.status) + ' ctype=' + ct)
            except Exception as e:
                log('FAIL ' + n + ' ' + type(e).__name__ + ' ' + str(e)[:120])
        browser.close()
        return True

result = run(True)
if not result:
    log('falling back to headed mode...')
    run(False)
log('DONE')
logf.close()
