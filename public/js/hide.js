	function addStdHidden(){
		$("#standard").fadeOut();//="standard-hidden";
		rmvCustHidden();
	}
	
	function addCustHidden(){
		$("#custom").fadeOut();//="custom-hidden";
		rmvStdHidden();
	}
	
	function rmvStdHidden()
	{$("#standard").fadeIn();//="standard";}
	}
	function rmvCustHidden()
	{$("#custom").fadeIn();//="custom";}
	}