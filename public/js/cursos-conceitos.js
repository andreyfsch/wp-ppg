var tabs = document.querySelector('#tabs_cursos');
if (tabs) {
	var instance = M.Tabs.init(tabs, {"swipable": true});
	var primeiro_id = document.querySelector('#card-cursos-content').querySelector('div:first-child').id;
	instance.select(primeiro_id);
	instance.updateTabIndicator();
}