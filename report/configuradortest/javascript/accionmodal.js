localStorage.setItem("action", true);

const createTest = (id) => {
    const action_a = localStorage.getItem("action");

    if(action_a == "false"){
        console.log('Create test: ', id);
        cancelTest(id);
        location.reload();
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

