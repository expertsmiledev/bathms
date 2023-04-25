// JavaScript Document

$(function () {
	//Save customer details
	var previous,
		ins_id = $(".inspectionForm").attr("id");
	$(
		".inspectionForm input:text:not(input[name='date']), .inspectionForm textarea, .inspectionForm select"
	)
		.on("focus", function () {
			previous = $(this).val();
		})
		.on("change", function () {
			var fieldName = $(this).attr("name"),
				fieldValue = $(this).val(),
				que_id;
			if ($(this).hasClass("inspQuestion")) {
				que_id = fieldName.substr(fieldName.indexOf("_") + 1);
				saveQuestionDetails(ins_id, que_id, fieldValue, $(this), previous);
			} else {
				if(fieldName != "ins_group")
					saveInspectionDetails(ins_id, fieldName, fieldValue, $(this), previous);
				else {
					var new_link = "/certificate/inspection/"+$("#ins_id").val()+"/"+$(this).val();
					$(".pdfLink").attr('href', new_link);
				}
			}
		});
	$(".inspectionForm input:checkbox").on("click", function () {
		var fieldName = $(this).attr("name"),
			fieldValue = $(this).prop("checked");
		previous = !fieldValue;
		saveInspectionDetails(
			ins_id,
			fieldName,
			fieldValue ? 1 : 0,
			$(this),
			previous
		);
	});
	//show datepicker on date field

	$("input[name='date']")
		.datepicker({
			dateFormat: "dd/mm/yy",
			altField: "input[name='ins_date']",
			altFormat: "yy-mm-dd",
		})
		.on("click", function () {
			previous = $("input[name='ins_date']").val();
		})
		.on("change", function () {
			var field = $("input[name='ins_date']"),
				fieldName = "ins_date",
				fieldValue = field.val();
			saveInspectionDetails(ins_id, fieldName, fieldValue, field, previous);
		});
	//Pass All Button
	$("#passAllButton").on("click", function () {
		$(this)
			.parent()
			.find("select")
			.each(function () {
				$(this).val("PASS");
			});
		loopPassAll(0);
	});
	//validate form before printing certificate
	$(".printCertificate a").on("click", function (e) {
		var v = validateForm();
		if (v !== true) {
			e.preventDefault();
			alert(v);
		} else {
			e.preventDefault();
			location.href = $(".pdfLink").attr("href");
		}
	});
});

function saveInspectionDetails(ins_id, name, value, el, previous) {
	var url = "/inspection/update_inspection_details",
		dat = { ins_id: ins_id, name: name, value: value };
	$.post(url, dat, function (data) {
		if (data == "_failed_") {
			alert("Data save failed");
			if (el.is(":checkbox")) {
				el.prop("checked", previous);
			} else if (el.is("textarea")) {
				el.text(previous);
			} else {
				el.val(previous);
			}
		} else {
			if (el.is(":checkbox")) {
				el.prop("checked", data == 0 ? false : true);
			} else if (el.is("textarea")) {
				el.text(data);
			} else {
				el.val(data);
			}
		}
	});
}
function saveQuestionDetails(ins_id, que_id, answer, el, previous) {
	var url = "/inspection/update_question_details",
		dat = { ins_id: ins_id, que_id: que_id, que_answer: answer };
	$.post(url, dat, function (data) {
		console.log(data);
		if (data == "_failed_") {
			alert("Data save failed");
			el.val(previous);
		} else {
			el.val(data);
		}
	});
}

function validateForm() {
	var fieldNames = {
		    ins_group: "Group Select",
			date: "Inspection Date",
			ins_jobnumber: "BAT Job Number",
			ins_custpo: "Customer PO Number",
			ins_testpressure: "Test Pressure",
			ins_workingpressure: "Working Pressure",
			ins_testtime: "Test Time",
			ins_ohms_a: "OHMS End A",
			ins_ohms_b: "OHMS End B",
			ins_ohms_overall: "OHMS Total",
			ins_instruction: "Instruction",
			ins_certresult: "Certificate Result",
		},
		msg = "Please enter the following fields:\n",
		valid = true;
	$(
		"#inspectionForm input[type!='hidden']:visible, #inspectionForm select:visible"
	).each(function () {
		var name = $(this).attr("name");
		if ($(this).val() === "") {
			if (name.search(/que_/) > -1) {
				nameArr = name.split("_");
				msg += "Question " + nameArr[1] + "\n";
			} else {
				msg += fieldNames[name] + "\n";
			}
			valid = false;
		}
	});
	if (!valid) return msg;
	return true;
}

function loopPassAll(index) {
	var $q = $(".inspQuestion"),
		len = $q.length;

	if (index < len) {
		$q.eq(index).trigger("change");
		index++;
		setTimeout(function () {
			loopPassAll(index);
		}, 200);
	}
}


