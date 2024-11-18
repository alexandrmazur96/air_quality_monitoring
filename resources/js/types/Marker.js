const TYPE_USER = 'user';
const TYPE_AIR_QUALITY = 'air_quality';

export class Marker {
    latitude;
    longitude;
    type;
    airQuality;
    airQualityIdxType;
    airQualityIdx;

    constructor(latitude, longitude, type, airQuality = null, airQualityIdxType = null, airQualityIdx = null) {
        this.validateParams(latitude, longitude, type, airQuality, airQualityIdxType, airQualityIdx);

        this.latitude = latitude;
        this.longitude = longitude;
        this.type = type;
        this.airQuality = airQuality;
        this.airQualityIdx = airQualityIdx;
        this.airQualityIdxType = airQualityIdxType;
    }

    validateParams(latitude, longitude, type, airQuality, airQualityIdxType, airQualityIdx) {
        if (type !== TYPE_USER && type !== TYPE_AIR_QUALITY) {
            throw new Error(`Invalid marker type: ${type}`);
        }
        if (latitude < -90 || latitude > 90) {
            throw new Error(`Invalid latitude: ${latitude}`);
        }
        if (longitude < -180 || longitude > 180) {
            throw new Error(`Invalid longitude: ${longitude}`);
        }
        if (type === TYPE_AIR_QUALITY && airQualityIdx < 0) {
            throw new Error(`Air quality index should be positive number: ${airQualityIdx}`);
        }

        if (type === TYPE_AIR_QUALITY) {
            if (airQuality === null) {
                throw new Error(`Air quality is required for marker type ${type}`);
            }
            if (airQualityIdxType === null) {
                throw new Error(`Air quality index type is required for marker type ${type}`);
            }
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

    get airQuality() {
        return this.airQuality;
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
            if (this.airQualityIdxType === 'aqi_us' && this.airQualityIdx > 300) {
                return 'images/markers/m-air-quality-aqi_us-300-plus.svg';
            }

            return `images/markers/m-air-quality-${this.airQualityIdxType}-${Math.round(parseFloat(this.airQualityIdx))}.svg`;
        }
    }
}
