function showToast(
  variant = "info",
  title = "Notification",
  message = "This is a toast message"
) {
  const variants = {
    success: "border-green-500 text-green-500",
    error: "border-red-500 text-red-500",
    warning: "border-yellow-500 text-yellow-500",
    info: "border-blue-500 text-blue-500",
  };

  // Create toast container
  const toast = document.createElement("div");
  toast.className = `fixed bg-background top-5 right-5 z-50 max-w-sm p-4 rounded-lg shadow-lg flex items-start gap-3 border-2 transition-transform duration-300 ease-in-out transform translate-x-5 opacity-0 ${variants[variant] || variants.info}`;

  // Toast content
  toast.innerHTML = `
    <strong class="font-bold">${title}</strong>
    <span class="block text-sm">${message}</span>
  `;

  // Append to body
  document.body.appendChild(toast);

  // Animate in
  setTimeout(() => {
    toast.classList.remove("translate-x-5", "opacity-0");
  }, 50);

  // Remove toast after 3 seconds
  setTimeout(() => {
    toast.classList.add("translate-x-5", "opacity-0");
    setTimeout(() => toast.remove(), 300);
  }, 4000);
}
