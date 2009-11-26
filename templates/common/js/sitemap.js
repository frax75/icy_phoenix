if(window.addEventListener)
{
	window.addEventListener('load', sitemap, false);
	window.addEventListener('load', readPrefs, false);
}
else
{
	window.attachEvent('onload', function(){
		sitemap();
		readPrefs();
		})
}

//			:::		START MAKE SITEMAP INTERACTIVE	:::

function sitemap()
{
	// grab all h2 elements
	var h = document.getElementsByTagName('h2');
	// grab all unordered lists
	var u = document.getElementsByTagName('ul');

	for(i = 0; i < u.length; i++)
	{
		// hide all unordered lists with 'sitemap' class
		if(u[i].className == 'sitemap')
		{
			u[i].style.display = 'none';
		}

		// get all links
		var a = u[i].getElementsByTagName('a');
		for(z = 0; z < a.length; z++)
		{
			// checks if link has a class of 'parent'
			if(a[z].className == 'parent')
			{
				var li = a[z].parentNode;
				// creates maximise.gif element
				var img = document.createElement('img');
					img.className = 'icon';
					img.src = 'templates/common/images/maximise.gif';
					img.style.verticalAlign = 'middle';

				li.insertBefore(img, a[z]);
				// set style
				li.className = 'parent';

				//hide child unordered list
				ul = a[z].nextSibling;
				while (ul.nodeType != 1)
				{
					ul = ul.nextSibling;
				}

				ul.style.display = 'none';

				// make clicking new image hide/show child unordered list
				img.onclick = function()
				{
					li = this.parentNode;
					ul = li.getElementsByTagName('ul')[0];
					var ulStatus = (ul.style.display == 'none') ? 'block' : 'none';
					ul.style.display = ulStatus;

					// toggle between maximise.gif and minimise.gif
					imgStatus = (ulStatus == 'block') ? 'minimise' : 'maximise';
					this.src = 'templates/common/images/switch_' + imgStatus + '.gif';
				}
			}
		}

	}

	for(x = 0; x < h.length; x++)
	{
		// assign unique IDS to each h2 element
		h[x].id = 'h2' + x;
		h[x].className = 'maximise';

		// make h2 element show/hide unordered list when clicked
		h[x].onclick = function()
		{
			var ul = this.nextSibling;

			while (ul.nodeType != 1)
			{
				ul = ul.nextSibling;
			}

			var ulStatus = (ul.style.display == 'none') ? 'block' : 'none';

			ul.style.display = ulStatus;
			var hStatus = (ulStatus == 'block') ? 'minimise' : 'maximise';
			this.className = hStatus;

			// set cookie
			return writePrefs(this.id,ulStatus);
		}
	}
}

//			:::		END MAKE SITEMAP INTERACTIVE	:::

//			:::		START WRITE HIDE/SHOW COOKIE :::

function writePrefs(section,tf)
{
	var cookieName = section;
	var today = new Date();
	var expires = new Date(today.getTime() + 10 * 24 * 60 * 60 * 1000);
	var index = (document.cookie != document.cookie) ? document.cookie.indexOf(cookieName) : -1;

	if (document.cookie)
	{
		var index = document.cookie.indexOf(cookieName);
		if (index != -1)
		{
			var namestart = (document.cookie.indexOf("=", index) + 1);

			if (document.cookie.substring(namestart) == tf)
			{
				return false;
			}
		}
	}

	document.cookie= section + " = " + tf + "; expires=" + expires.toGMTString();
}

//			::: END WRITE HIDE/SHOW COOKIE :::

//			::: START READ HIDE/SHOW COOKIE :::

function readPrefs()
{
	// grab all h2 elements
	var h = document.getElementsByTagName('h2');

	// check cookie for hide/show preferences
	for(i=0; i<h.length; i++)
	{
		// gets the element after the h2 heading
		var ul = h[i].nextSibling;
		h[i].id = 'h2' + i;

		// makes sure ul is an element, not a blank space or carriage return
		while (ul.nodeType != 1)
		{
			ul = ul.nextSibling;
		}

		var cookieName = 'h2' + i;

		if (document.cookie.length > 0)
		{
			var begin = document.cookie.indexOf(cookieName+"=");
			if (begin != -1)
			{
				begin += cookieName.length+1;
				var end = document.cookie.indexOf(";", begin);

				if (end == -1) end = document.cookie.length;

				// gets display status from cookie
				var secValue = unescape(document.cookie.substring(begin, end));

				// sets dispaly status to equal that which was in the cookie
				var secStatus = (secValue == 'none') ? 'none' : 'block';
				var h2Img = (secValue == 'none') ? 'maximise' : 'minimise';

				document.getElementById(cookieName).className = h2Img;
				ul.style.display = secStatus;
			}
		}
	}
}

//			::: END READ HIDE/SHOW COOKIE :::
