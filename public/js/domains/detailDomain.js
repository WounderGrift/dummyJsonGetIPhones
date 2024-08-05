export class DetailDomain {
    constructor(initValues = null) {
        this.gameId     = null;
        this.gameName   = null;
        this.series     = null;
        this.categories = null;
        this.release    = false;
        this.checkboxes = {
            isPublic: false,
            isSponsor: false,
            isSoft: false,
            isWaiting: false,
            isWeak: false,
        };

        this.avatarGrid    = null;
        this.avatarPreview = null;
        this.avatarTrailer = null;
        this.getAvatarPreviewFromScreen = false;
        this.dateRelease = null;
        this.torrentsNew = {};
        this.torrentsOld = {};
        this.screenshotsNew = {};

        this.summaryObject = null;
        this.description   = null;
        this.requireObject  = null;

        this.previewTrailer = null;
        this.trailer = null;

        Object.assign(this, initValues);
    }
}
