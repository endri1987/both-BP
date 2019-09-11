		function setDoc(d,f) {

					if (document.layers)
					{
					 if (document.dwin1.visibility == "hide")
						document.dwin1.visibility = "show";
					 else
					 	document.dwin1.visibility == "hide"
					}
					else if (document.all)
					{
					 if (dwin1.style.visibility == "hidden")
						dwin1.style.visibility = "visible";
					 else
						dwin1.style.visibility = "hidden"
					}

		}