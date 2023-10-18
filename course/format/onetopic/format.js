// Javascript functions for onetopic course format
M.course = M.course || {};

M.course.format = M.course.format || {};

M.course.format.showInfo = function(id) {

    new M.core.dialogue({
                draggable: true,
                headerContent: '<span>' + M.util.get_string('info', 'moodle') + '</span>',
                bodyContent: Y.Node.one('#' + id),
                centered: true,
                width: '480px',
                modal: true,
                visible: true
            });

    Y.Node.one('#' + id).show();

};

M.course.format.dialogueinitloaded = false;

M.course.format.dialogueinit = function() {

    if (M.course.format.dialogueinitloaded) {
        return;
    }

    M.course.format.dialogueinitloaded = true;
    Y.all('[data-infoid]').each(function(node) {
        node.on('click', function(e) {
            e.preventDefault();
            M.course.format.showInfo(node.getAttribute('data-infoid'));
        });
    });
};

require(['jquery'], function($) {
	$('.modtype_label').click(function() { $(this).nextAll().each(function(index) {
		   if (($(this).attr('class') + "").includes("modtype_label"))  return false;
		   $(this).slideToggle();
		})      
	});
	$('.modtype_label').css('cursor', 'pointer');
	$(".rui-contentafterlink").find("h4").append("&nbsp;&nbsp;&nbsp;&nbsp;<img src='/course/format/onetopic/down.svg' style='width: 20px'>");
	$(".course-teachers-box").hide();
	
	$('.modtype_label').click();
});