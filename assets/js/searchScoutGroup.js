$(document).ready(function() {
	var cache = {};
	console.log("Hallo");

	$('input[type="text"]').autocomplete({
		minLength: 2,
		source: function(request, response) {
			var term = request.term;
			if(term in cache) {
				response(cache[term]);
				return;
			}

			$.ajax({
				url: "/searchScoutGroup/"+request.term,
				dataType: "JSON",
				success: function (data) {
					cache[term] = data;
					response(data);
				}
			});
		},
		select: function(event, ui) {
			$('input[name="group_id"]').val(ui.item.id);
		}
	});
});
