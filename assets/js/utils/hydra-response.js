export function handlerHydraResponse(data) {
  return new HydraResponse(data);
}

export class HydraResponse {
  emptyModel() {
    return {
      "@context": null,
      "@id": null,
      "@type": "hydra:Collection",
      [this.prefix + "totalItems"]: 0,
      [this.prefix + "member"]: [],
    };
  }

  constructor(data, prefix = "hydra") {
    this.data = data;
    this.prefix = prefix;

    if (!this.data || !this.data["@context"]) {
      console.error("No @context from hydra data");
      // fixed data
      this.data = this.emptyModel();
    }
  }

  get prefix() {
    return this._prefix;
  }

  set prefix(value) {
    this._prefix = value + ":";
  }

  get data() {
    return this._data;
  }

  set data(value) {
    this._data = value;
  }

  getTotalItems() {
    return this.data[this.prefix + "totalItems"];
  }

  getMember() {
    const members = this.data[this.prefix + "member"];
    return members;
  }

  isEmpty() {
    return !this.getTotalItems() || this.getMember().length;
  }
}
