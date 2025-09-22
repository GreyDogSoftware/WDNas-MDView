// Code related to the clipboard goes here.
class clipboardHandler{
    static #_defaultButtonClass = 'btn btn-sm btn-outline-secondary clipboard-copy';
    static #_defaultButtonContent = '<i class="bi bi-copy"/>';
    static #notificationsHandler = null;

    static copy(source_id) {
        // obtener el texto del div
        let query = '[data-clipboard-id="'+source_id+'"]';
        let nodes =  document.querySelectorAll(query);
        if(nodes.length==1){
            try {
                this.copyToClipboard(nodes[0].innerText);
                if (this.#notificationsHandler !== null){this.#notificationsHandler.showInfo('Text copied to the clipboard!');}
                console.log('Text copied to the clipboard!');
            } catch(error) {
                if (this.#notificationsHandler !== null){this.#notificationsHandler.showError('Error copying while the text. ' + error);}
                console.error(error);
            }
        }
    }
    static copyText(text){
        try {
            this.copyToClipboard(text);
            if (this.#notificationsHandler !== null){this.#notificationsHandler.showInfo('Text copied to the clipboard!');}
            console.log('Text copied to the clipboard!');
        } catch(error) {
            if (this.#notificationsHandler !== null){this.#notificationsHandler.showError('Error copying while the text. ' + error);}
            console.error(error);
        }
    }
    static async copyToClipboard(textToCopy) {
        // Navigator clipboard api needs a secure context (https)
        if (navigator.clipboard && window.isSecureContext) {
            await navigator.clipboard.writeText(textToCopy);
        }else{
            // Use the 'out of viewport hidden text area' trick
            const textArea = document.createElement("textarea");
            textArea.value = textToCopy;
            // Move textarea out of the viewport so it's not visible
            textArea.style.position = "absolute";
            textArea.style.left = "-999999px";
            document.body.prepend(textArea);
            textArea.select();
            try {
                document.execCommand('copy');
            } catch (error) {
                console.error(error);
            } finally {
                textArea.remove();
            }
        }
    }
    static AddButtons(querySelector){
        // Tries to add buttons next to the code blocks
        const collection =  document.querySelectorAll(querySelector);
        if(collection.length>0){
            console.log('Marked ' + collection.length + ' elements for clipboard use.');
            let max=999999;
            let min=0;
            collection.forEach(el => {
                let rnd_id_high=Math.floor(Math.random() * (max - min) ) + min;
                let rnd_id_low=Math.floor(Math.random() * (max - min) ) + min;
                let clipboardId = rnd_id_low+'_'+rnd_id_high;
                el.setAttribute('data-clipboard-id',clipboardId);
                var copyButton = document.createElement("button");
                copyButton.setAttribute("type", "button");
                copyButton.setAttribute("class", clipboardHandler.#_defaultButtonClass);
                copyButton.setAttribute("onclick", this.name+'.copy("'+clipboardId+'")');
                copyButton.innerHTML= clipboardHandler.#_defaultButtonContent;
                el.appendChild(copyButton);
            });
        }else{
            //console.log('No elements found');
        }
    }
    static SetNotificationHandler (handler){
        if (handler!==null){
            if(typeof handler.show !== 'function') { return;}
            if(typeof handler.showInfo !== 'function') { return;}
            if(typeof handler.showError !== 'function') { return;}
            this.#notificationsHandler = handler;
        }
    }
}