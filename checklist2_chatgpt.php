const DEBUG = true;
let checklistId = null;


/* =========================
   INIT
========================= */
document.addEventListener('DOMContentLoaded', initChecklistPage);

async function initChecklistPage() {
    await loadItems();
    await loadChecklists();
    bindEvents();
}


/* =========================
   EVENTS
========================= */
function bindEvents() {

    // dropdown switch
    document.getElementById('checklistSelect')
        .addEventListener('change', handleChecklistChange);

    // submit form
    document.getElementById('create_list')
        .addEventListener('submit', handleSave);
}


/* =========================
   UI EVENTS
========================= */
function handleChecklistChange(event) {

    const selectedId = event.target.value;

    if (selectedId === "") {
        checklistId = null;
        document.getElementById("create_list").style.display = "block";
        document.getElementById("update_list").style.display = "none";
        return;
    }

    checklistId = selectedId;

    if (DEBUG) {
        console.log("Geselecteerde checklist:", checklistId);
    }

    document.getElementById("create_list").style.display = "none";
    document.getElementById("update_list").style.display = "block";

    loadChecklistItems(checklistId);
}


/* =========================
   DATA LOADERS
========================= */
async function loadChecklists() {
    try {
        const response = await fetch('API/get_checklists.php');
        const data = await response.json();

        const select = document.getElementById('checklistSelect');

        select.innerHTML = '<option value="">-- nieuwe checklist --</option>';

        data.forEach(item => {
            const option = document.createElement('option');
            option.value = item.id;
            option.textContent = item.titel + " (" + item.jaar + ")";
            select.appendChild(option);
        });

    } catch (error) {
        console.error("Fout bij laden checklists:", error);
    }
}


async function loadItems() {
    try {
        const response = await fetch('API/get_items.php');
        const data = await response.json();

        const foodList = document.getElementById('foodList');
        const stuffList = document.getElementById('stuffList');

        foodList.innerHTML = '';
        stuffList.innerHTML = '';

        data.forEach(item => {

            const li = document.createElement('li');
            li.className = 'list-group-item';

            li.innerHTML = `
                <input class="form-check-input me-2"
                    type="checkbox"
                    name="${item.categorie}[]"
                    value="${item.id}"
                    id="${item.categorie}_${item.id}">

                <label for="${item.categorie}_${item.id}">
                    ${item.naam}
                </label>
            `;

            if (item.categorie === 'food') {
                foodList.appendChild(li);
            } else {
                stuffList.appendChild(li);
            }
        });

    } catch (error) {
        console.error("Fout bij laden items:", error);
    }
}


async function loadChecklistItems(id) {
    try {

        if (DEBUG) console.log("Load checklist items:", id);

        const response = await fetch('API/get_checklist_items.php?checklist_id=' + id);
        const data = await response.json();

        document.querySelectorAll('#foodList input, #stuffList input')
            .forEach(cb => cb.checked = false);

        data.forEach(item => {

            const checkboxId = item.categorie + '_' + item.item_id;
            const checkbox = document.getElementById(checkboxId);

            if (checkbox) {
                checkbox.checked = item.checked == 1;
            }
        });

    } catch (error) {
        console.error("Fout bij laden checklist items:", error);
    }
}


/* =========================
   SAVE (CREATE / UPDATE)
========================= */
async function handleSave(event) {
    event.preventDefault();

    const form = event.target;
    const formData = new FormData(form);

    const data = {};
    formData.forEach((value, key) => {
        data[key] = value;
    });

    let url = 'API/create_checklist.php';

    if (checklistId !== null) {
        data.id = checklistId;
        url = 'API/update_checklist.php';
    }

    if (DEBUG) {
        console.log("SAVE DATA:", data);
        console.log("URL:", url);
    }

    try {
        const response = await fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (DEBUG) console.log("API RESULT:", result);

        if (response.ok) {

            if (checklistId === null) {
                checklistId = result.id;
            }

            let input = document.querySelector('input[name="checklist_id"]');

            if (!input) {
                input = document.createElement("input");
                input.type = "hidden";
                input.name = "checklist_id";
                document.getElementById("update_list").appendChild(input);
            }

            input.value = checklistId;

            document.getElementById("update_list").style.display = "block";
        }

    } catch (error) {
        console.error("Fout:", error);
    }
}