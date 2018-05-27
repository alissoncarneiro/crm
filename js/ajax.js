function XMLHTTPRequest() {
  try {
    return new XMLHttpRequest();
  } catch(ee) {
    try {
      return new ActiveXObject("Msxml2.XMLHTTP");
    } catch(e) {
      try {
        return new ActiveXObject("Microsoft.XMLHTTP");
      } catch(E) {
        return false;
      }
    }
  }
}
