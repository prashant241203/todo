document.addEventListener('click',function(e){
    if (!e.target.classList.contains('completeBtn')) {
        return;
    }
    
    let completeBtn = e.target;
    let card = completeBtn.closest(".task-card");
    let form = completeBtn.closest("form");
    let id = form.querySelector("input[name = 'id']").value;

    card.querySelectorAll("button").forEach(btn => {
        btn.disabled = true;
    }); 
            
    let title = card.querySelector("h3");
    title.style.textDecoration = "line-through";
    title.style.color = "grey";
    
    completeBtn.innerText = "completed";
    
    fetch("index.php", {
        method :"post",
        headers : {"Content-Type":"application/x-www-form-urlencoded"},
        body : "ajax_complete=1&id=" + id
    })
    .catch(() =>{
        card.querySelectorAll("button").forEach(btn => btn.disabled = false);
        title.style.textDecoration = "none";
        title.style.color = "";
        alert("error"); 
    });
});

$(document).ready(function(){
    $('#search_results').hide();
    $('#search_query').on('keyup',function(){
        let query = $(this).val().trim();
        if (query !== "") {
            
            $('#all_tasks').hide();
            $.ajax({
                url : "search.php",
                method : "POST",
                data : {query : query},
                success : function(data){
                    $('#search_results').html(data).show();
                }
            });
        }else{
            $('#search_results').empty().hide();
            $('#all_tasks').show();
        }
    })
})

$('.filter-select').on('change', function(){

       let category = $('#filter_category').val().trim();
       let priority = $('#filter_priority').val().trim();
       let status = $('#filter_status').val().trim();

        if (category !== '' || priority !== '' || status !== '') {
            $('#all_tasks').hide();
            $('#search_results').hide();

     $.ajax({   
         url : "fetch_filter.php",
         method : "POST",
         data : {
            category : category,
            priority : priority,
            status : status
         },
         success : function(response){
             $('#filter-data').html(response).show()
         }
    });
    }else{
         $('#filter-data').empty().hide();
         $('#all_tasks').show();    
    }
})
