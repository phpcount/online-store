import { getCookie, setCookie } from "../../../utils/cookie-manager";

window.toggleFiltersVisibility = function (section) {
  const filtersKey = "filtersVisible_" + section;
  const filtersSaveValue = getCookie(filtersKey);

  const visibleValue = filtersSaveValue === "false";

  setCookie(filtersKey, visibleValue, { secure: true, "max-age": 3600 });
};

window.changeFiltersBlockVisibility = function (section, el) {
  const filtersKey = "filtersVisible_" + section;
  const filtersSaveValue = getCookie(filtersKey);

  el.style.display = filtersSaveValue === "false" ? "block" : "none";
};
