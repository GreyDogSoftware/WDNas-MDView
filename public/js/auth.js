class AuthHandler{
    #APIEndpoint = 'api/?act=set_repokey';
    createAuth(repoKey, repoSecret){
        let paramString = 'repo=' + repoKey + '&secret=' + repoSecret;
        this.makeRequest('', paramString, null);
    }
    makeRequest(url, paramString, callback){
        let xhr = new XMLHttpRequest();
        xhr.open('POST',this.#APIEndpoint+url, true);
        xhr.responseType = 'json';
        var params = 'orem=ipsum&name=binny';
        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        xhr.onload = function() {
            var status = xhr.status;
            // add a callback here?
        };
        xhr.send(paramString);
    };
}