"use strict";

const checkTemas = document.querySelectorAll('input[name="check-tema"]');
const checkDif = document.querySelectorAll('input[name="check-dif"]');
const checkTipo = document.querySelectorAll('input[name="check-tipo"]');
const checkCant = document.querySelectorAll('input[name="check-cant"]');
const checkDur = document.querySelectorAll('input[name="check-dur"]');
const btn = document.querySelector('#btn-generar'); 
let temas = [];
let dificultad = [];
let tipo = [];
let cantidad = '';
let duracion = '';
var text = '';
var estado = [false, false, false, false, false];
btn.disabled = true;

checkTemas.forEach((checkbox, index) => {
    let check_tema_all = document.getElementById("btn-check-all");
    checkbox.addEventListener("change", () => { 
        if (checkbox.checked) {
            if(index === 0){
                temas[index] = "Todos";
                for(let i=0; i<checkTemas.length - 1; i++){
                    temas[i + 1] = "";
                    document.getElementById(`btn-check-${i + 1}`).checked = false;
                } 
            } else {
                check_tema_all.checked = false;
                temas[0] = "";
                temas[index] = index;
            }
            estado[0] =  true;
            if(estado.includes(true) && !estado.includes(false)){btn.disabled = false;}
        }else{
            var valuesTemasChecked = [];
            var checkTemasChecked = document.querySelectorAll('input[name="check-tema"]:checked');
            Array.prototype.forEach.call(checkTemasChecked, function(el) {
                valuesTemasChecked.push(el.value);
            });
            var anyCheckTemas =  valuesTemasChecked.includes('on');
            if(!anyCheckTemas) {
                estado[0] =  false;
                if(estado.includes(false)){btn.disabled = true;}
            }
            delete temas[index];
        }
    });
});

checkDif.forEach((checkbox, index) => {
    let check_dif_todos = document.getElementById("btn-check-todos");
    let check_dif_facil = document.getElementById("btn-check-facil");
    let check_dif_medio = document.getElementById("btn-check-medio");
    let check_dif_dificil = document.getElementById("btn-check-dificil");
    checkbox.addEventListener("change", () => { 
        if (checkbox.checked) {
            switch (index){
                case 0:
                    dificultad[index] = 'Todos';
                    dificultad[1] = '';
                    dificultad[2] = '';
                    dificultad[3] = '';
                    check_dif_facil.checked = false;
                    check_dif_medio.checked = false;
                    check_dif_dificil.checked = false;
                    break;
                case 1:
                    dificultad[index] = 'Facil';
                    dificultad[0] = '';
                    check_dif_todos.checked = false;
                    break;
                case 2:
                    dificultad[index] = 'Medio';
                    dificultad[0] = '';
                    check_dif_todos.checked = false;
                    break;
                case 3:
                    dificultad[index] = 'Dificil';
                    dificultad[0] = '';
                    check_dif_todos.checked = false;
                    break;
            }
            estado[1] =  true;
            if(estado.includes(true) && !estado.includes(false)){btn.disabled = false;}
        }else{
            var valuesDifChecked = [];
            var checkDifChecked = document.querySelectorAll('input[name="check-dif"]:checked');
            Array.prototype.forEach.call(checkDifChecked, function(el) {
                valuesDifChecked.push(el.value);
            });
            var anyCheckDif =  valuesDifChecked.includes('on');
            if(!anyCheckDif) {
                estado[1] =  false;
                if(estado.includes(false)){btn.disabled = true;}
            }
            delete dificultad[index];
        }
    });
});

checkTipo.forEach((checkbox, index) => {
    let check_type_todos = document.getElementById("btn-check-type-todos");
    let check_type_falladas = document.getElementById("btn-check-type-falladas");
    let check_type_sin_responder = document.getElementById("btn-check-type-sin-responder");
    let check_type_riesgo = document.getElementById("btn-check-type-riesgo");
    checkbox.addEventListener("change", () => { 
        if (checkbox.checked) {
            switch (index){
                case 0:
                    tipo[index] = 'Todos';
                    tipo[1] = '';
                    tipo[2] = '';
                    tipo[3] = '';
                    check_type_falladas.checked = false;
                    check_type_sin_responder.checked = false;
                    check_type_riesgo.checked = false;
                    break;
                case 1:
                    tipo[index] = 'Falladas';
                    tipo[0] = '';
                    check_type_todos.checked = false;
                    break;
                case 2:
                    tipo[index] = 'Sin responder';
                    tipo[0] = '';
                    check_type_todos.checked = false;
                    break;
                case 3:
                    tipo[index] = 'Con riesgo';
                    tipo[0] = '';
                    check_type_todos.checked = false;
                    break;
            }
            estado[2] =  true;
            if(estado.includes(true) && !estado.includes(false)){btn.disabled = false;}
        }else{
            var valuesTipoChecked = [];
            var checkTipoChecked = document.querySelectorAll('input[name="check-tipo"]:checked');
            Array.prototype.forEach.call(checkTipoChecked, function(el) {
                valuesTipoChecked.push(el.value);
            });
            var anyCheckTipo =  valuesTipoChecked.includes('on');
            if(!anyCheckTipo) {
                estado[2] =  false;
                if(estado.includes(false)){btn.disabled = true;}
            }
            delete tipo[index];
        }
    });
});

checkCant.forEach((checkbox, index) => {
    let check_cant_c10 = document.getElementById("btn-check-c10");
    let check_cant_c20 = document.getElementById("btn-check-c20");
    let check_cant_c30 = document.getElementById("btn-check-c30");
    let check_cant_c50 = document.getElementById("btn-check-c50");
    let check_cant_c70 = document.getElementById("btn-check-c70");
    let check_cant_c100 = document.getElementById("btn-check-c100");
    checkbox.addEventListener("change", () => { 
        if (checkbox.checked) {
            switch (index){
                case 0:
                    cantidad = '10';
                    check_cant_c20.checked = false;
                    check_cant_c30.checked = false;
                    check_cant_c50.checked = false;
                    check_cant_c70.checked = false;
                    check_cant_c100.checked = false;
                    break;
                case 1:
                    cantidad = '20';
                    check_cant_c10.checked = false;
                    check_cant_c30.checked = false;
                    check_cant_c50.checked = false;
                    check_cant_c70.checked = false;
                    check_cant_c100.checked = false;
                    break;
                case 2:
                    cantidad = '30';
                    check_cant_c20.checked = false;
                    check_cant_c10.checked = false;
                    check_cant_c50.checked = false;
                    check_cant_c70.checked = false;
                    check_cant_c100.checked = false;
                    break;
                case 3:
                    cantidad = '50';
                    check_cant_c20.checked = false;
                    check_cant_c30.checked = false;
                    check_cant_c10.checked = false;
                    check_cant_c70.checked = false;
                    check_cant_c100.checked = false;
                    break;
                case 4:
                    cantidad = '70';
                    check_cant_c20.checked = false;
                    check_cant_c30.checked = false;
                    check_cant_c50.checked = false;
                    check_cant_c10.checked = false;
                    check_cant_c100.checked = false;
                    break;
                case 5:
                    cantidad= '100';
                    check_cant_c20.checked = false;
                    check_cant_c30.checked = false;
                    check_cant_c50.checked = false;
                    check_cant_c70.checked = false;
                    check_cant_c10.checked = false;
                    break;
            }
        }
        var valuesCantChecked = [];
        var checkCantChecked = document.querySelectorAll('input[name="check-cant"]:checked');
        Array.prototype.forEach.call(checkCantChecked, function(el) {
            valuesCantChecked.push(el.value);
        });
        var anyCheckCant =  valuesCantChecked.includes('on');
        if(anyCheckCant) {
            estado[3] =  true;
            if(estado.includes(true) && !estado.includes(false)){btn.disabled = false;}
        } else {
            estado[3] =  false;
            if(estado.includes(false)){btn.disabled = true;}
        }
    });
});

checkDur.forEach((checkbox, index) => {
    let check_dur_d10 = document.getElementById("btn-check-d10");
    let check_dur_d25 = document.getElementById("btn-check-d25");
    let check_dur_d40 = document.getElementById("btn-check-d40");
    let check_dur_d50 = document.getElementById("btn-check-d50");
    let check_dur_d70 = document.getElementById("btn-check-d70");
    let check_dur_dst = document.getElementById("btn-check-dst");
    checkbox.addEventListener("change", () => { 
        if (checkbox.checked) {
            switch (index){
                case 0:
                    duracion = '10';
                    check_dur_d25.checked = false;
                    check_dur_d40.checked = false;
                    check_dur_d50.checked = false;
                    check_dur_d70.checked = false;
                    check_dur_dst.checked = false;
                    break;
                case 1:
                    duracion = '25';
                    check_dur_d10.checked = false;
                    check_dur_d40.checked = false;
                    check_dur_d50.checked = false;
                    check_dur_d70.checked = false;
                    check_dur_dst.checked = false;
                    break;
                case 2:
                    duracion = '40';
                    check_dur_d25.checked = false;
                    check_dur_d10.checked = false;
                    check_dur_d50.checked = false;
                    check_dur_d70.checked = false;
                    check_dur_dst.checked = false;
                    break;
                case 3:
                    duracion = '50';
                    check_dur_d25.checked = false;
                    check_dur_d40.checked = false;
                    check_dur_d10.checked = false;
                    check_dur_d70.checked = false;
                    check_dur_dst.checked = false;
                    break;
                case 4:
                    duracion = '70';
                    check_dur_d25.checked = false;
                    check_dur_d40.checked = false;
                    check_dur_d50.checked = false;
                    check_dur_d10.checked = false;
                    check_dur_dst.checked = false;
                    break;
                case 5:
                    duracion = 'sintiempo';
                    check_dur_d25.checked = false;
                    check_dur_d40.checked = false;
                    check_dur_d50.checked = false;
                    check_dur_d70.checked = false;
                    check_dur_d10.checked = false;
                    break;
            }
        }
        var valuesDurChecked = [];
        var checkDurChecked = document.querySelectorAll('input[name="check-dur"]:checked');
        Array.prototype.forEach.call(checkDurChecked, function(el) {
            valuesDurChecked.push(el.value);
        });
        var anyCheckDur=  valuesDurChecked.includes('on');
        if(anyCheckDur) {
            estado[4] =  true;
            if(estado.includes(true) && !estado.includes(false)){btn.disabled = false;}
        } else {
            estado[4] =  false;
            if(estado.includes(false)){btn.disabled = true;}
        }
    });
});

btn.addEventListener("click", () => { 
    localStorage.setItem("temas", temas);
    localStorage.setItem("dificultad", dificultad);
    localStorage.setItem("tipo", tipo);
    localStorage.setItem("cantidad", cantidad);
    localStorage.setItem("duracion", duracion);
});

