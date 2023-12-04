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

checkTemas.forEach((checkbox, index) => {
    let check_tema = document.getElementById(`btn-check-${index + 1}`);
    let check_tema_all = document.getElementById("btn-check-all");
    checkbox.addEventListener("change", () => { 
        if (checkbox.checked) {
            if(index === 0) {
                temas[index] = "todos";
                for(let i=0; i<checkTemas.length - 1; i++){
                    document.getElementById(`btn-check-${i + 1}`).setAttribute("disabled", true);
                }
            } else{
                text = check_tema.nextElementSibling.textContent;
                temas[index] = Number(text) - 1;
                check_tema_all.setAttribute("disabled", true);
            }
        }else{
            delete temas[index];
            if(
                temas.includes(`btn-check-${index + 1}`)
            ) {
                check_tema_all.setAttribute("disabled", true);
            }else{
                check_tema_all.removeAttribute("disabled");
            }
            for(let i=0; i<checkTemas.length - 1; i++){
                document.getElementById(`btn-check-${i + 1}`).removeAttribute("disabled");
            }
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
                    dificultad[index] = 'todos';
                    check_dif_facil.setAttribute("disabled", true);
                    check_dif_medio.setAttribute("disabled", true);
                    check_dif_dificil.setAttribute("disabled", true);
                    break;
                case 1:
                    dificultad[index] = 'facil';
                    check_dif_todos.setAttribute("disabled", true);
                    break;
                case 2:
                    dificultad[index] = 'medio';
                    check_dif_todos.setAttribute("disabled", true);
                    break;
                case 3:
                    dificultad[index] = 'dificil';
                    check_dif_todos.setAttribute("disabled", true);
                    break;
            }
        }else{
            delete dificultad[index];
            if(
                dificultad.includes('facil') || 
                dificultad.includes('medio') || 
                dificultad.includes('dificil')
            ) {
                check_dif_todos.setAttribute("disabled", true);
            }else{
                check_dif_todos.removeAttribute("disabled");
            }
            check_dif_facil.removeAttribute("disabled");
            check_dif_medio.removeAttribute("disabled");
            check_dif_dificil.removeAttribute("disabled");
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
                    tipo[index] = 'todos';
                    check_type_falladas.setAttribute("disabled", true);
                    check_type_sin_responder.setAttribute("disabled", true);
                    check_type_riesgo.setAttribute("disabled", true);
                    break;
                case 1:
                    tipo[index] = 'falladas';
                    check_type_todos.setAttribute("disabled", true);
                    break;
                case 2:
                    tipo[index] = 'sinresponder';
                    check_type_todos.setAttribute("disabled", true);
                    break;
                case 3:
                    tipo[index] = 'riesgo';
                    check_type_riesgo.setAttribute("disabled", true);
                    break;
            }
        }else{
            delete tipo[index];
            if(
                tipo.includes('falladas') || 
                tipo.includes('sinresponder') || 
                tipo.includes('riesgo')
            ) {
                check_type_todos.setAttribute("disabled", true);
            }else{
                check_type_todos.removeAttribute("disabled");
            }
            check_type_falladas.removeAttribute("disabled");
            check_type_sin_responder.removeAttribute("disabled");
            check_type_riesgo.removeAttribute("disabled");
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
                    check_cant_c20.setAttribute("disabled", true);
                    check_cant_c30.setAttribute("disabled", true);
                    check_cant_c50.setAttribute("disabled", true);
                    check_cant_c70.setAttribute("disabled", true);
                    check_cant_c100.setAttribute("disabled", true);
                    break;
                case 1:
                    cantidad = '20';
                    check_cant_c10.setAttribute("disabled", true);
                    check_cant_c30.setAttribute("disabled", true);
                    check_cant_c50.setAttribute("disabled", true);
                    check_cant_c70.setAttribute("disabled", true);
                    check_cant_c100.setAttribute("disabled", true);
                    break;
                case 2:
                    cantidad = '30';
                    check_cant_c10.setAttribute("disabled", true);
                    check_cant_c20.setAttribute("disabled", true);
                    check_cant_c50.setAttribute("disabled", true);
                    check_cant_c70.setAttribute("disabled", true);
                    check_cant_c100.setAttribute("disabled", true);
                    break;
                case 3:
                    cantidad = '50';
                    check_cant_c10.setAttribute("disabled", true);
                    check_cant_c20.setAttribute("disabled", true);
                    check_cant_c30.setAttribute("disabled", true);
                    check_cant_c70.setAttribute("disabled", true);
                    check_cant_c100.setAttribute("disabled", true);
                    break;
                case 4:
                    cantidad = '70';
                    check_cant_c10.setAttribute("disabled", true);
                    check_cant_c20.setAttribute("disabled", true);
                    check_cant_c30.setAttribute("disabled", true);
                    check_cant_c50.setAttribute("disabled", true);
                    check_cant_c100.setAttribute("disabled", true);
                    break;
                case 5:
                    cantidad= '100';
                    check_cant_c10.setAttribute("disabled", true);
                    check_cant_c20.setAttribute("disabled", true);
                    check_cant_c30.setAttribute("disabled", true);
                    check_cant_c50.setAttribute("disabled", true);
                    check_cant_c70.setAttribute("disabled", true);
                    break;
            }
        }else{
            check_cant_c10.removeAttribute("disabled");
            check_cant_c20.removeAttribute("disabled");
            check_cant_c30.removeAttribute("disabled");
            check_cant_c50.removeAttribute("disabled");
            check_cant_c70.removeAttribute("disabled");
            check_cant_c100.removeAttribute("disabled");
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
                    check_dur_d25.setAttribute("disabled", true);
                    check_dur_d40.setAttribute("disabled", true);
                    check_dur_d50.setAttribute("disabled", true);
                    check_dur_d70.setAttribute("disabled", true);
                    check_dur_dst.setAttribute("disabled", true);
                    break;
                case 1:
                    duracion = '25';
                    check_dur_d10.setAttribute("disabled", true);
                    check_dur_d40.setAttribute("disabled", true);
                    check_dur_d50.setAttribute("disabled", true);
                    check_dur_d70.setAttribute("disabled", true);
                    check_dur_dst.setAttribute("disabled", true);
                    break;
                case 2:
                    duracion = '40';
                    check_dur_d10.setAttribute("disabled", true);
                    check_dur_d25.setAttribute("disabled", true);
                    check_dur_d50.setAttribute("disabled", true);
                    check_dur_d70.setAttribute("disabled", true);
                    check_dur_dst.setAttribute("disabled", true);
                    break;
                case 3:
                    duracion = '50';
                    check_dur_d10.setAttribute("disabled", true);
                    check_dur_d25.setAttribute("disabled", true);
                    check_dur_d40.setAttribute("disabled", true);
                    check_dur_d70.setAttribute("disabled", true);
                    check_dur_dst.setAttribute("disabled", true);
                    break;
                case 4:
                    duracion = '70';
                    check_dur_d10.setAttribute("disabled", true);
                    check_dur_d25.setAttribute("disabled", true);
                    check_dur_d40.setAttribute("disabled", true);
                    check_dur_d50.setAttribute("disabled", true);
                    check_dur_dst.setAttribute("disabled", true);
                    break;
                case 5:
                    duracion = 'sintiempo';
                    check_dur_d10.setAttribute("disabled", true);
                    check_dur_d25.setAttribute("disabled", true);
                    check_dur_d40.setAttribute("disabled", true);
                    check_dur_d50.setAttribute("disabled", true);
                    check_dur_d70.setAttribute("disabled", true);
                    break;
            }
        }else{
            check_dur_d10.removeAttribute("disabled");
            check_dur_d25.removeAttribute("disabled");
            check_dur_d40.removeAttribute("disabled");
            check_dur_d50.removeAttribute("disabled");
            check_dur_d70.removeAttribute("disabled");
            check_dur_dst.removeAttribute("disabled");
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

