var GetURL = function(url, callback) {
  var xhr = new XMLHttpRequest();
  xhr.open('GET', url, true);
  xhr.responseType = 'json';
  xhr.onload = function() {
    var status = xhr.status;
    if (status === 200) {
    callback(null, xhr.response);
    } else {
    callback(status, xhr.response);
    }
  };
  xhr.send();
};

function LoadRepos(){
  var DstURL='api/?act=get_repos';
  GetURL(DstURL, function(err, data){
    if (err !== null) {
      console.log('Something went wrong: ' + err);
    }else{
      if (data['content'].length>0){
        let templateSource = document.getElementById("template_repoItem");
        var target = document.getElementById("doc_list");

        target.innerHTML="";
        for (var x=0; x < data['content'].length; x++){
          if (data['content'][x]['file_type']){
            let div = templateSource.content.cloneNode(true).querySelector('li');

            let repoNameTarget = div.querySelector('span#template-repo-name');
            repoNameTarget.innerText = data['content'][x]['file_name'];	// Setting the link text

            let repoDescTarget = div.querySelector('span#template-repo-description');
            repoDescTarget.innerHTML = '&nbsp;'; // Needs to be set as html or won't work
            if(data['content'][x]['description']){repoDescTarget.innerText=data['content'][x]['description']}

            let repoIcon ='bi bi-database'; // Default icon
            if(data['content'][x]['protected']==true){repoIcon='bi bi-database-lock'}
            if(data['content'][x]['available']==false){repoIcon='bi bi-database-slash'}

            let repoIconTarget = div.querySelector('i.template-item-icon');
            repoIconTarget.setAttribute('class', repoIcon);	// Setting the link icon

            let currentAction = "LoadFiles('"+data['content'][x]['path_rela']+"')";
            let repoActionTarget = div.querySelector('a');
            repoActionTarget.setAttribute("onclick", currentAction); // Setting the link action

            target.appendChild(div);	// Appending new children to DOM
          }
        }
      }
    }
  });
}

var LoadMD = function(url,filename) {
  //console.log('LOAD_URL: ' + url);
  //console.log('LOADFILE: ' + filename);
  var target_content = document.getElementById("doc_content");
  // The server streams the file content directly, so there's no parsin involved.
  target_content.setAttribute("src", 'api/?act=get_content&path='+url);
  var target_name = document.getElementById("doc_filename");
  if(filename){
    target_name.innerHTML=filename;
  }else{
    target_name.innerHTML="";
  }
};

function AddButtons(){
  // Tries to add buttons next to the code blocks
  //const collection =  document.querySelectorAll("pre.language-bash");
  const collection =  document.querySelectorAll('pre[class^="language-"]');
  //console.log(collection.length);
  let max=999999;
  let min=0;
  collection.forEach(el => {
    let rnd_id_high=Math.floor(Math.random() * (max - min) ) + min;
    let rnd_id_low=Math.floor(Math.random() * (max - min) ) + min;
    let clipboardId = rnd_id_low+'_'+rnd_id_high;
    el.setAttribute('data-clipboard-id',clipboardId);
    var groupkey = document.createElement("button");
    groupkey.setAttribute("class", "btn btn-sm btn-outline-secondary clipboard-copy");
		groupkey.setAttribute("type", "button");
    groupkey.setAttribute("onclick",'ClipboardAdd("'+clipboardId+'")');
    groupkey.innerHTML='<i class="bi bi-copy"/>';
    el.appendChild(groupkey);
  });
};

const Toast={
  _obj: null,
  create: function(){
    const toastLiveExample = document.getElementById('toast-window')
    _obj = bootstrap.Toast.getOrCreateInstance(toastLiveExample)
  },
  setContent: function(body, title){
    const toastLiveExample = document.getElementById('toast-window')
    let notifTitle = toastLiveExample.querySelector('#idx_toast-title');
    let notifBody =  toastLiveExample.querySelector('.toast-body');
    notifTitle.innerText=title;
    notifBody.innerText=body;
  },
  show(){
    _obj.show();
  }
}




function ClipboardAdd(source_id) {
  // obtener el texto del div
  let query = 'pre[data-clipboard-id="'+source_id+'"]';
  let nodes =  document.querySelectorAll(query);
  //console.log(nodes.length);
  if(nodes.length==1){
    //console.log(nodes[0].innerText);
      // copiar al portapapeles

      try {
          copyToClipboard(nodes[0].innerText);
          Toast.setContent('Text copied to the clipboard!','MD-View')
          Toast.show();
          console.log('Text copied to the clipboard!');
      } catch(error) {
          Toast.setContent('Error copying while the text. ' + error,'MD-View')
          Toast.show();
          console.error(error);
      }

    /*navigator.clipboard.writeText(nodes[0].innerText)
    .then(() => {
      Toast.setContent('Text copied','MD-View')
      Toast.show();
      //alert("Texto copiado al portapapeles!");
    }).catch(err => {
      console.error("Error al copiar: ", err);
      Toast.setContent('Error copying while the text ' + err,'MD-View')
      Toast.show();
    });*/
  }
};

async function copyToClipboard(textToCopy) {
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



function LoadFiles(source){
  var DstURL='api/?act=get_files&path='+source;
  GetURL(DstURL, function(err, data){
    if (err !== null) {
      console.log('Something went wrong: ' + err);
    }else{
      if (data['content'].length>0){
        let templateSource = document.getElementById("template_menuItem");
        var target = document.getElementById("doc_list");
        target.innerHTML="";
        for (var x=0; x < data['content'].length; x++){
          if (data['content'][x]['file_type']){
            let div = templateSource.content.cloneNode(true).querySelector('li');
            let item_name = div.querySelector('span.template-item-text');
            let item_icon = div.querySelector('i.template-item-icon');
            let item_action = div.querySelector('a');

            let currentIcon, currentText, currentAction;
            if (data['content'][x]['file_type']=='file'){
              // File
              currentAction= "LoadMD('"+data['content'][x]['path_rela']+"','"+data['content'][x]['file_name']+"')";
              currentIcon="bi bi-file-earmark-code";
            }else if (data['content'][x]['file_type']=='dir'){
              // Folder
              currentAction= "LoadFiles('"+data['content'][x]['path_rela']+"')";
              currentIcon="bi bi-folder";
            }else{
              // Empty
              currentIcon="bi bi-ban";
            }

            item_name.textContent = data['content'][x]['file_name'];	// Setting the link text
            item_icon.setAttribute("class", currentIcon);	// Setting the link icon
            item_action.setAttribute("onclick", currentAction); // Setting the link action
            target.appendChild(div);	// Appending new children to DOM
                        }
        }
      }
    }
  });
}