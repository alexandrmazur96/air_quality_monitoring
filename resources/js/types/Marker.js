const TYPE_USER = 'user';
const TYPE_AIR_QUALITY = 'air_quality';

export class Marker {
    latitude;
    longitude;
    type;
    airQualityIdx;

    constructor(latitude, longitude, type, airQualityIdx = null) {
        this.validateParams(latitude, longitude, type, airQualityIdx);

        this.latitude = latitude;
        this.longitude = longitude;
        this.type = type;
        this.airQualityIdx = airQualityIdx;
    }

    validateParams(latitude, longitude, type, airQualityIdx) {
        if (type !== TYPE_USER && type !== TYPE_AIR_QUALITY) {
            throw new Error(`Invalid marker type: ${type}`);
        }
        if (latitude < -90 || latitude > 90) {
            throw new Error(`Invalid latitude: ${latitude}`);
        }
        if (longitude < -180 || longitude > 180) {
            throw new Error(`Invalid longitude: ${longitude}`);
        }
        if (type === TYPE_AIR_QUALITY) {
            if (airQualityIdx === null) {
                throw new Error(`Air quality index is required for marker type ${type}`);
            }
            if (airQualityIdx < 0) {
                throw new Error(`Invalid air quality index: ${airQualityIdx}`);
            }
        }
    }

    get latitude() {
        return this.latitude;
    }

    get longitude() {
        return this.longitude;
    }

    static get TYPE_USER() {
        return TYPE_USER;
    }

    static get TYPE_AIR_QUALITY() {
        return TYPE_AIR_QUALITY;
    }

    getIcon() {
        if (this.type === TYPE_USER) {
            return 'images/markers/m-user.svg';
        }
        if (this.type === TYPE_AIR_QUALITY) {
            return `images/markers/m-air-quality-${this.airQualityIdx}.svg`;
        }
    }
}
