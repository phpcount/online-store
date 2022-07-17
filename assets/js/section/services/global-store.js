window.setDataInGlobalObj = function (varName, data) {
  if (typeof window[varName] === "undefined") {
    window[varName] = { ...data };
  } else {
    window[varName] = { ...window[varName], ...data };
  }
};
