// Code related to the clipboard goes here.

const Clip={
    _defaultButtonClass: 'btn btn-sm btn-outline-secondary clipboard-copy',
    copy: function(source_id) {
        // obtener el texto del div
        let query = '[data-clipboard-id="'+source_id+'"]';
        let nodes =  document.querySelectorAll(query);
        if(nodes.length==1){
            try {
                this.copyToClipboard(nodes[0].innerText);
                Toast.setContent('Text copied to the clipboard!','MD-View')
                Toast.show();
                console.log('Text copied to the clipboard!');
            } catch(error) {
                Toast.setContent('Error copying while the text. ' + error,'MD-View')
                Toast.show();
                console.error(error);
            }
        }
    },
    copyToClipboard: async function(textToCopy) {
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
    },
    AddButtons: function(querySelector){
        // Tries to add buttons next to the code blocks
        const collection =  document.querySelectorAll(querySelector);
        console.log(this.constructor.name)
        if(collection.length>0){
            console.log('Marked ' + collection.length + ' elements for clipboard use.');
            let max=999999;
            let min=0;
            collection.forEach(el => {
                let rnd_id_high=Math.floor(Math.random() * (max - min) ) + min;
                let rnd_id_low=Math.floor(Math.random() * (max - min) ) + min;
                let clipboardId = rnd_id_low+'_'+rnd_id_high;
                el.setAttribute('data-clipboard-id',clipboardId);
                var groupkey = document.createElement("button");
                groupkey.setAttribute("class", this._defaultButtonClass);
                groupkey.setAttribute("type", "button");
                groupkey.setAttribute("onclick",'Clip.copy("'+clipboardId+'")');
                groupkey.innerHTML='<i class="bi bi-copy"/>';
                el.appendChild(groupkey);
            });
        }else{
            console.log('No elements found');
        }
    }
}