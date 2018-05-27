c=0;
du="";
function escondediv(dv,n){
	for(i=1;i<=n;i++){
		if(i==dv ){
			if(du!=dv){
				$("#mdiv"+i).slideDown('normal');
				du=dv;
			}else{
			   du="";
				$("#mdiv"+i).slideUp('normal');
			}
		}else{
			$("#mdiv"+i).slideUp('normal');
		}
	}
}
function reveza(qq){
	$("#"+qq).attr("class","itens_menu_r");
}
function volta(qq){
	$("#"+qq).attr("class","itens_menu");
}
