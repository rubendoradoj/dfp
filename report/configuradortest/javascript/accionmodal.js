const createTest = (id) => {

    const temas = localStorage.getItem("temas");
    const dificultad = localStorage.getItem("dificultad");
    const tipo = localStorage.getItem("tipo");
    const cantidad = localStorage.getItem("cantidad");
    const duracion = localStorage.getItem("duracion");

    console.log('Creating test: ', [temas, dificultad, tipo, cantidad, duracion]);
    cancelTest(id);
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

