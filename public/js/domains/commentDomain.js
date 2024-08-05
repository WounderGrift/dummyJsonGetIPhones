export class CommentDomain {
    constructor(initValues = null) {
        let whom_id = null;
        let game_id = null;
        let quote   = null;
        let comment = null;

        Object.assign(this, initValues);
    }
}
