function bmAvatarShowInitials(img)
{
	if(!img)
		return;

	var parent = img.parentNode;
	var initials = img.dataset ? (img.dataset.initials || '') : '';
	var bgPrimary = img.dataset && img.dataset.bgPrimary === '1';

	img.remove();

	if(!parent || initials === '')
		return;

	parent.style.padding = '';
	parent.style.overflow = '';
	parent.appendChild(document.createTextNode(initials));
	if(bgPrimary)
		parent.classList.add('bg-primary-lt');
}

function bmAvatarInitialsFallback(img)
{
	bmAvatarShowInitials(img);
}

function bmAvatarFallback(img)
{
	if(!img || !img.dataset)
		return;

	if(!img.dataset.fbs)
	{
		img.dataset.fbs = '1';
		if(img.dataset.gravatar)
		{
			img.src = img.dataset.gravatar;
			return;
		}
	}

	bmAvatarShowInitials(img);
}
