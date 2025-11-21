import {npubEncode} from "nostr-tools/nip19";

export default () => ({

    async init() {

    },

    async openNostrLogin() {
        const pubkey = await window.nostr.getPublicKey();
        const npub = npubEncode(pubkey);
        console.log(pubkey);
        console.log(npub);
        this.$dispatch('nostrLoggedIn', {pubkey: npub});
    },

});
