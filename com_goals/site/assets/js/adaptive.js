	window.onload = setScreenClass; 
	window.onresize = setScreenClass;

	//  Following transition classes will be declared:
	//
	//	classname		  container width
	//	------------------------------------------
	//	mobile_ver   	  240px			
	//	mobile_hor		  320px			
	//	screen_ultralow	  320px -  550px	
	//	screen_low		  550px -  800px	
	//	screen_med		  800px - 1024px	
	//	screen_hi		 1024px - 1280px	
	//	screen_wide				> 1280px			

	function setScreenClass(){
		var fmt = document.getElementById('goals-wrap').clientWidth;
		var cls = (fmt<=240)?'mobile_ver':(fmt>240&&fmt<=320)?'mobile_hor':(fmt>320&&fmt<=550)?'screen_ultralow':(fmt>550&&fmt<=800)?'screen_low':(fmt>800&&fmt<=1024)?'screen_med':(fmt>1024&&fmt<=1280)?'screen_high':'screen_wide';
        document.getElementById('debug-count').innerHTML=fmt+'px &rarr; '+cls; // debug line
		document.getElementById('goals-wrap').className=cls;
	};
    
    
$(document).ready(function(){   
        /*today - task checkboxes*/
        $(".check-done").click(function(){
            
        if ($(this).is(":checked")) {
            $(this).parents(".check-task-task").removeClass("checkbox_off").addClass("checkbox_on");
            // change label color in tasks:
            $(this).parents(".goals-right-task").find("label").addClass("goals-task-green").html("accomplished");
            $(this).attr("value", "yes");
            //console.log($(this).val());
        }
    
        else {
            $(this).parents(".check-task-task").removeClass("checkbox_on").addClass("checkbox_off");
            $(this).parents(".goals-right-task").find("label").removeClass("goals-task-green").html("to be done");
            $(this).attr("value", "no");
           //console.log($(this).val());
    }
                    
    });
    
        /*today - task and overdue toggle*/
        $(".goals-task-toggle").click(function(){
            $(".goals-task").css("display", "block");          
            $(".goals-overdue").css("display", "none");
        });

        $(".goals-overdue-toggle").click(function(){          
            $(".goals-task").css("display", "none");
            $(".goals-overdue").css("display", "block");  
        });
        
        /*today - habits for today checkboxes*/
        $(".check-task-habits").click(function(){
            if($(this).hasClass("checkbox_off_good")){
                $(this).addClass("checkbox_on_good").removeClass("checkbox_off_good");
        console.log($(this).attr('class'));
            }
            else{
                if($(this).hasClass("checkbox_on_good")){
                 $(this).addClass("checkbox_off_good").removeClass("checkbox_on_good");
        console.log($(this).attr('class'));
            }
                else{
                    if($(this).hasClass("checkbox_off_bad")){
                    $(this).addClass("checkbox_on_bad").removeClass("checkbox_off_bad");
            console.log($(this).attr('class'));
                }
                    else{
                        if($(this).hasClass("checkbox_on_bad")){
                        $(this).addClass("checkbox_off_bad").removeClass("checkbox_on_bad");
                console.log($(this).attr('class'));
                    }}

                }
                }
            
        });
            
        
})