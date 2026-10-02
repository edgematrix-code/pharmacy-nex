from curl_cffi import requests as cr

targets = ['chrome', 'chrome136', 'chrome131', 'chrome124', 'chrome120', 'chrome110', 'chrome99',
           'safari18_0', 'safari17_0', 'safari15_5', 'firefox135', 'firefox133', 'edge101']
url = 'https://nexuspharma.to/'
for t in targets:
    try:
        r = cr.get(url, impersonate=t, timeout=25)
        print(t, '->', r.status_code, len(r.content))
    except Exception as e:
        print(t, '-> ERR', type(e).__name__, str(e)[:90])
