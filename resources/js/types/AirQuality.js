export class AirQuality {
    provider;
    pm10;
    pm2_5;
    nh3;
    o3;
    no;
    no2;
    so2;
    co;
    updated_at;

    constructor(provider, pm10, pm2_5, nh3, o3, no, no2, so2, co, updated_at) {
        this.provider = provider;
        this.pm10 = pm10;
        this.pm2_5 = pm2_5;
        this.nh3 = nh3;
        this.o3 = o3;
        this.no = no;
        this.no2 = no2;
        this.so2 = so2;
        this.co = co;
        this.updated_at = updated_at;
    }

    get provider() {
        return this.provider;
    }

    get pm10() {
        return this.pm10;
    }

    get pm2_5() {
        return this.pm2_5;
    }

    get nh3() {
        return this.nh3;
    }

    get o3() {
        return this.o3;
    }

    get no() {
        return this.no;
    }

    get no2() {
        return this.no2;
    }

    get so2() {
        return this.so2;
    }

    get co() {
        return this.co;
    }

    get updated_at() {
        return this.updated_at;
    }
}
