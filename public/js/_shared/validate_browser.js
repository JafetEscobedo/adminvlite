// Verificar navegador
if ((function () {
  /* Cadena con información del navegador */
  const browserInfo = window.navigator.userAgent;

  /* Si la cadena de información incluye MSIE
   se asume que es la versión 10 o anterior de IE */
  if (browserInfo.indexOf("MSIE") > 0) {
    return true;
  }

  /* Si la cadena de información incluye Trident/
   se asume que es la versión 11 de IE */
  if (browserInfo.indexOf("Trident/") > 0) {
    return true;
  }

  /* En cualquier otro caso se asume que
   el navegador no es alguna versión de IE */
  return false;
})()) {
  document.documentElement.innerHTML = "<h1 align='center'>Navegador incompatible, use uno diferente</h1>";
  document.execCommand("Stop");
}