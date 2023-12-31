localStorage.setItem("action", true);

const createTest = (id) => {
    const action_a = localStorage.getItem("action");
    const temas = localStorage.getItem('temas');
    const dificultad = localStorage.getItem('dificultad');
    const tipo = localStorage.getItem('tipo');
    const cantidad = localStorage.getItem('cantidad');
    const duracion = localStorage.getItem('duracion');
    var url_redirect = "";

    var new_tem = "";

    let arr_tem = temas.split(','); 
    let new_tem_clean = arr_tem.filter(el => el != '');
    if(new_tem_clean.length > 1){
        new_tem = new_tem_clean.join(', ');
    } else {
        new_tem = new_tem_clean.join('');
    }

    let arr_dif = dificultad.split(','); 
    let new_dif = arr_dif.filter(el => el != '');

    let arr_tip = tipo.split(','); 
    let new_tip = arr_tip.filter(el => el != '');

    var vars =  "id=" + id.toString() + 
                "&temas=" + new_tem + 
                "&dificultad=" + new_dif +
                "&tipo=" + new_tip +
                "&cantidad=" + cantidad +
                "&duracion=" + duracion +
                "&state=create";

    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'classes/utils/utils.php', false);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhr.onload = function(){
        const response = this.responseText;
        url_redirect = response;
    }
    xhr.send(vars);

    if(action_a == "false"){
        cancelTest(id);
        window.location.href = url_redirect;
    }
}

const cancelTest = (id) => {
    var modal = document.querySelectorAll(".show");
    var modalbody = document.querySelectorAll(`.course-${id}`);

    [].forEach.call(modalbody, function(el) {
        el.classList.remove("modal-open");
    });

    [].forEach.call(modal, function(el) {
        el.className = el.className.replace(/\bshow\b/, "hide");
    });
    
    console.log('Cancel test: ', id);
}

