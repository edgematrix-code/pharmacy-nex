import time
from playwright.sync_api import sync_playwright

LOG = r'C:\Users\HP\Herd\vitalis\storage\cf_state.log'
f = open(LOG, 'w', encoding='utf-8')

def log(m):
    f.write(str(m) + '\n')
    f.flush()

UA = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36'

with sync_playwright() as pw:
    b = pw.chromium.launch(channel='msedge', headless=False,
                           args=['--disable-blink-features=AutomationControlled',
                                 '--window-position=-2400,-2400', '--window-size=1280,900'])
    ctx = b.new_context(user_agent=UA, locale='en-US', viewport={'width': 1280, 'height': 900})
    p = ctx.new_page()
    try:
        p.goto('https://nexuspharma.to/', wait_until='domcontentloaded', timeout=90000)
    except Exception as e:
        log('goto err ' + str(e)[:150])
    passed = False
    for i in range(40):
        try:
            t = p.title()
        except Exception as e:
            t = 'ERR ' + str(e)[:60]
        log(str(i) + ' title=' + t)
        if 'Just a moment' not in t and 'Attention' not in t:
            passed = True
            break
        for fr in p.frames:
            if 'challenges.cloudflare.com' in fr.url:
                log('  cf frame: ' + fr.url[:120])
                try:
                    fr.click('input[type=checkbox]', timeout=1200)
                    log('  clicked checkbox')
                except Exception:
                    pass
                try:
                    fr.click('label', timeout=800)
                except Exception:
                    pass
        time.sleep(2)
    try:
        p.screenshot(path=r'C:\Users\HP\Herd\vitalis\storage\cf_state.png', full_page=False)
    except Exception as e:
        log('shot err ' + str(e)[:100])
    log('passed=' + str(passed) + ' final title=' + p.title())
    log('cookies=' + str([c['name'] for c in ctx.cookies()]))
    b.close()
log('DONE')
f.close()
