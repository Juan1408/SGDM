import time
import uno
from com.sun.star.beans import PropertyValue

def prop(name, value):
    p = PropertyValue()
    p.Name = name
    p.Value = value
    return p

local_ctx = uno.getComponentContext()
resolver = local_ctx.ServiceManager.createInstanceWithContext(
    "com.sun.star.bridge.UnoUrlResolver", local_ctx)

ctx = None
for _ in range(30):
    try:
        ctx = resolver.resolve(
            "uno:socket,host=127.0.0.1,port=2002;urp;StarOffice.ComponentContext")
        break
    except Exception:
        time.sleep(0.5)

if ctx is None:
    raise SystemExit("No se pudo conectar con LibreOffice")

desktop = ctx.ServiceManager.createInstanceWithContext(
    "com.sun.star.frame.Desktop", ctx)

url = "file:///C:/xampp/htdocs/SGDM/presentacion/ASCEND_Defensa.pptx"
doc = desktop.loadComponentFromURL(url, "_blank", 0, (prop("Hidden", True),))
controller = doc.getCurrentController()
pages = doc.getDrawPages()
total = pages.getCount()

for i in range(total):
    controller.setCurrentPage(pages.getByIndex(i))
    out = "file:///C:/xampp/htdocs/SGDM/presentacion/render/slide-%02d.png" % (i + 1)
    doc.storeToURL(out, (prop("FilterName", "impress_png_Export"),))
    print("exported", i + 1)

doc.close(False)
print("done", total)
