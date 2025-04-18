/**
 * This script handles and suppresses common console errors,
 * particularly those from Chrome extensions trying to communicate with pages
 */
(function () {
  // Store the original console.error function
  const originalConsoleError = console.error;

  // Override console.error to filter out specific errors
  console.error = function () {
    // Check if this is the "Receiving end does not exist" error
    if (
      arguments.length > 0 &&
      typeof arguments[0] === "string" &&
      (arguments[0].includes("Receiving end does not exist") ||
        arguments[0].includes("Could not establish connection"))
    ) {
      // Silently ignore these errors
      return;
    }

    // For all other errors, call the original function
    return originalConsoleError.apply(console, arguments);
  };

  // Handle unhandled promise rejections that might be related to extensions
  window.addEventListener("unhandledrejection", function (event) {
    if (
      event.reason &&
      typeof event.reason.message === "string" &&
      (event.reason.message.includes("Receiving end does not exist") ||
        event.reason.message.includes("Could not establish connection"))
    ) {
      // Prevent the default handling of the error
      event.preventDefault();
    }
  });
})();
