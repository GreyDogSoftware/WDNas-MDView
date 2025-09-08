class Square extends HTMLElement {

    static get observedAttributes() {
        return ['content'];
    }
    constructor(val) {
        super();
        console.log('inside constructor');
        this.attachShadow({mode: 'open'});
        this.shadowRoot.appendChild(document.createElement('button'));

        this.button = this.shadowRoot.querySelector('button');
        this.button.className = "square";

        this.content = val;

        console.log('constructor ended');
    }

    get content() {
        console.log('inside getter');
        return this.button.getAttribute('content');
    }
    set content(val) {
        console.log('setter being executed, val being: ', val);
        // pass null to represent empty square
        if (val !== null) {
            this.button.setAttribute('content', val);

        } else {
            if (this.button.hasAttribute('content')) {
                this.button.removeAttribute('content');
            }
        }

    }
    connectedCallback() {
        //console.log('connected callback being executed now');
    }

    // not working :(
    attributeChangedCallback(name, oldValue, newValue) {
        console.log('attribute changed callback being executed now');
        if (name === 'content') {
            this.button.innerHTML = newValue?newValue:" ";
        }
    }
}
customElements.define('square-box', Square);