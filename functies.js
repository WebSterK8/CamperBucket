

function Inputbeveiliging($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}



function initPdfDownload(buttonId, contentId, filename = 'export.pdf') {
  const button = document.getElementById(buttonId);
  const element = document.getElementById(contentId);
  if (!button || !element) return;

  button.addEventListener('click', () => {
    const options = {
      filename,
      margin: 1,
      image: { type: 'jpeg', quality: 0.95 },
      html2canvas: { scale: 2, useCORS: true },
      jsPDF: { unit: 'in', format: 'a4', orientation: 'portrait' }
    };
    html2pdf().set(options).from(element).save();
  });
}