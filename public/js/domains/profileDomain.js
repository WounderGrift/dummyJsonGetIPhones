export class ProfileDomain {
    constructor(initValues = null) {
        this.profileId = null;
        this.cid  = null;
        this.role = null;

        this.name     = null;
        this.email    = null;
        this.password = null;

        this.avatar = '';
        this.avatar_name = null;
        this.status   = null;
        this.about_me = null;

        this.get_letter_release = null;
        this.remember = false;
        this.timezone = null;

        Object.assign(this, initValues);
    }
}
