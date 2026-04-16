/**
 * Dynamic Gradient Generator
 * Generates infinite beautiful gradient combinations on-the-fly
 */

// Define variables at the top level to ensure they have the proper scope
// Check if MyColorGenerator is already defined globally to prevent duplicate declaration
if (
	typeof window.MyColorGenerator === "undefined" &&
	typeof MyColorGenerator === "undefined"
) {
	window.MyColorGenerator = {
		// Generate a random hue (0-360)
		randomHue() {
			return Math.floor(Math.random() * 360);
		},

		// Generate HSL color
		hslToHex(h, s, l) {
			l /= 100;
			const a = (s * Math.min(l, 1 - l)) / 100;
			const f = (n) => {
				const k = (n + h / 30) % 12;
				const color = l - a * Math.max(Math.min(k - 3, 9 - k, 1), -1);
				return Math.round(255 * color)
					.toString(16)
					.padStart(2, "0");
			};
			return `#${f(0)}${f(8)}${f(4)}`;
		},

		// Generate complementary colors (opposite on color wheel)
		complementary(baseHue) {
			const hue1 = baseHue;
			const hue2 = (baseHue + 180) % 360;
			return [
				this.hslToHex(hue1, 70 + Math.random() * 30, 50 + Math.random() * 20),
				this.hslToHex(hue2, 70 + Math.random() * 30, 50 + Math.random() * 20),
			];
		},

		// Generate analogous colors (adjacent on color wheel)
		analogous(baseHue) {
			const hue1 = baseHue;
			const hue2 = (baseHue + 30 + Math.random() * 30) % 360;
			return [
				this.hslToHex(hue1, 70 + Math.random() * 30, 50 + Math.random() * 20),
				this.hslToHex(hue2, 70 + Math.random() * 30, 50 + Math.random() * 20),
			];
		},

		// Generate triadic colors (120° apart)
		triadic(baseHue) {
			const hue1 = baseHue;
			const hue2 = (baseHue + 120) % 360;
			return [
				this.hslToHex(hue1, 70 + Math.random() * 30, 50 + Math.random() * 20),
				this.hslToHex(hue2, 70 + Math.random() * 30, 50 + Math.random() * 20),
			];
		},

		// Generate split-complementary colors
		splitComplementary(baseHue) {
			const hue1 = baseHue;
			const hue2 = (baseHue + 150 + Math.random() * 60) % 360;
			return [
				this.hslToHex(hue1, 70 + Math.random() * 30, 50 + Math.random() * 20),
				this.hslToHex(hue2, 70 + Math.random() * 30, 50 + Math.random() * 20),
			];
		},

		// Generate monochromatic colors (same hue, different saturation/lightness)
		monochromatic(baseHue) {
			const s1 = 60 + Math.random() * 40;
			const s2 = 60 + Math.random() * 40;
			const l1 = 40 + Math.random() * 20;
			const l2 = 50 + Math.random() * 30;
			return [this.hslToHex(baseHue, s1, l1), this.hslToHex(baseHue, s2, l2)];
		},
	}; // End of MyColorGenerator object
} // End of 'if (typeof window.MyColorGenerator === 'undefined' && typeof MyColorGenerator === 'undefined')' check

// Gradient generator
// Define GradientGenerator if it doesn't exist - note: MyColorGenerator must exist before this runs
// Also check if it's already defined globally to avoid conflicts
if (
	typeof window.MyColorGenerator !== "undefined" &&
	typeof window.GradientGenerator === "undefined"
) {
	window.GradientGenerator = {
		schemes: [
			"complementary",
			"analogous",
			"triadic",
			"splitComplementary",
			"monochromatic",
		],

		// Generate a single gradient
		generate() {
			const baseHue = window.MyColorGenerator.randomHue();
			const scheme =
				this.schemes[Math.floor(Math.random() * this.schemes.length)];
			const colors = window.MyColorGenerator[scheme](baseHue);

			// Ensure the left side is darker than the right side
			// Use to calculate brightness of a color
			function getBrightness(hex) {
				const r = parseInt(hex.substr(1, 2), 16);
				const g = parseInt(hex.substr(3, 2), 16);
				const b = parseInt(hex.substr(5, 2), 16);
				return (r * 299 + g * 587 + b * 114) / 1000;
			}

			let color1 = colors[0];
			let color2 = colors[1];

			// If color1 is lighter than color2, swap them to ensure darker on left
			if (getBrightness(color1) > getBrightness(color2)) {
				[color1, color2] = [color2, color1];
			}

			// Use a left-to-right gradient (90 degrees) to ensure consistent direction
			return `linear-gradient(90deg, ${color1} 0%, ${color2} 100%)`;
		},

		// Generate multiple unique gradients
		generateMultiple(count) {
			const gradients = [];
			for (let i = 0; i < count; i++) {
				gradients.push(this.generate());
			}
			return gradients;
		},
	};
} // End of 'if (typeof GradientGenerator === 'undefined')' check
// End of 'if (typeof MyColorGenerator === 'undefined')' check (this was removed to make both checks independent)

/**
 * Apply random gradients to elements matching the given selector
 * Generates unique gradients for each matching element
 */
function applyGradientsToSelector(
	selector,
	dataAttribute = "data-gradient-index"
) {
	// Get elements matching the selector
	const elements = document.querySelectorAll(selector);

	if (elements.length === 0) {
		return;
	}

	// Generate unique gradient for each element
	const gradients = window.GradientGenerator.generateMultiple(elements.length);

	// Apply gradients to each element
	elements.forEach((element, index) => {
		// Create a more specific style to override existing CSS
		element.style.setProperty("background", gradients[index], "important");
		element.style.setProperty("color", "#ffffff", "important");
		element.style.setProperty(
			"textShadow",
			"0 1px 2px rgba(0,0,0,0.2)",
			"important"
		);
		element.style.setProperty(
			"transition",
			"background 0.3s ease",
			"important"
		);

		// Optional: Add data attribute to track gradient
		element.setAttribute(dataAttribute, index);
	});
}

/**
 * Apply random gradients to both card headers and table headers
 * Generates unique gradients for each element
 */
function applyRandomGradients() {
	// Apply gradients to card headers
	applyGradientsToSelector(".card-header", "data-card-gradient-index");
}

// Execute when DOM is fully loaded
if (document.readyState === "loading") {
	document.addEventListener("DOMContentLoaded", applyRandomGradients);
} else {
	applyRandomGradients();
}

// Re-apply for dynamic content
function reapplyGradients() {
	applyRandomGradients();
}

// Re-apply to elements matching the selector
function reapplyGradientsToSelector(
	selector,
	dataAttribute = "data-gradient-index"
) {
	applyGradientsToSelector(selector, dataAttribute);
}

// Function to apply gradients to table headers when tables appear
function observeTableHeaders() {
	// Get all table headers that are dataTables
	const tableHeaders = document.querySelectorAll("table.dataTable > thead");

	tableHeaders.forEach((thead, index) => {
		const rows = thead.querySelectorAll("tr");

		if (rows.length > 1) {
			// Generate a single gradient for all rows in this thead
			const gradient = window.GradientGenerator.generate();

			// Apply the same gradient to all rows in the thead
			rows.forEach((row, rowIndex) => {
				row.style.setProperty("background", gradient, "important");
				row.style.setProperty("color", "#ffffff", "important");
				row.style.setProperty(
					"textShadow",
					"0 1px 2px rgba(0,0,0,0.2)",
					"important"
				);
				row.style.setProperty(
					"transition",
					"background 0.3s ease",
					"important"
				);
				row.setAttribute("data-table-gradient-index", `${index}-${rowIndex}`);
			});
		} else {
			// If only one row, apply individual gradients as before
			applyGradientsToSelector(
				"table.dataTable > thead > tr",
				"data-table-gradient-index"
			);
		}
	});
}

// Expose API for external use
if (typeof window.GradientAPI === "undefined") {
	window.GradientAPI = {
		apply: applyRandomGradients,
		reapply: reapplyGradients,
		applyToSelector: applyGradientsToSelector,
		reapplyToSelector: reapplyGradientsToSelector,
		observeTableHeaders: observeTableHeaders,
		generate: () => window.GradientGenerator.generate(),
		generateMultiple: (count) =>
			window.GradientGenerator.generateMultiple(count),
	};
} else {
	// If window.GradientAPI already exists, extend it with additional methods
	Object.assign(window.GradientAPI, {
		apply: applyRandomGradients,
		reapply: reapplyGradients,
		applyToSelector: applyGradientsToSelector,
		reapplyToSelector: reapplyGradientsToSelector,
		observeTableHeaders: observeTableHeaders,
		generate: () => window.GradientGenerator.generate(),
		generateMultiple: (count) =>
			window.GradientGenerator.generateMultiple(count),
	});
}

// Start observing table headers when the script is loaded
if (document.readyState === "loading") {
	document.addEventListener("DOMContentLoaded", function () {
		// Small delay to ensure DataTables have been initialized
		setTimeout(function () {
			if (
				window.GradientAPI &&
				typeof window.GradientAPI.observeTableHeaders === "function"
			) {
				window.GradientAPI.observeTableHeaders();
			}
		}, 100);
	});
} else {
	// DOM is already loaded, so run after a small delay to ensure DataTables are initialized
	setTimeout(function () {
		if (
			window.GradientAPI &&
			typeof window.GradientAPI.observeTableHeaders === "function"
		) {
			window.GradientAPI.observeTableHeaders();
		}
	}, 100);
}

// Function to handle AJAX loaded content
function handleAjaxLoadedContent() {
	// Reapply gradients to any new card headers
	applyGradientsToSelector(".card-header", "data-card-gradient-index");

	// Reapply gradients to any new table headers
	setTimeout(function () {
		observeTableHeaders();
	}, 100); // Delay to ensure DataTables are initialized
}

// Add the function to the API
if (window.GradientAPI) {
	window.GradientAPI.handleAjaxLoadedContent = handleAjaxLoadedContent;
}

// Listen for AJAX complete events to reapply gradients to dynamically loaded content
$(document).on("ajaxComplete", function () {
	if (
		window.GradientAPI &&
		typeof window.GradientAPI.handleAjaxLoadedContent === "function"
	) {
		window.GradientAPI.handleAjaxLoadedContent();
	}
});

// Export for module systems
if (typeof module !== "undefined" && module.exports) {
	module.exports = window.GradientAPI;
}

// // Also use a MutationObserver to catch any dynamically added elements
// if (typeof MutationObserver !== "undefined") {
// 	const observer = new MutationObserver(function (mutations) {
// 		let shouldUpdateGradients = false;

// 		mutations.forEach(function (mutation) {
// 			if (mutation.type === "childList" && mutation.addedNodes.length > 0) {
// 				for (let i = 0; i < mutation.addedNodes.length; i++) {
// 					const node = mutation.addedNodes[i];
// 					if (node.nodeType === 1) {
// 						// Element node
// 						// Check if added node or its children contain elements that need gradients
// 						if (
// 							node.querySelector &&
// 							(node.querySelector(".card-header") ||
// 								node.querySelector("table.dataTable"))
// 						) {
// 							shouldUpdateGradients = true;
// 							break;
// 						}
// 					}
// 				}
// 			}
// 		});

// 		if (shouldUpdateGradients) {
// 			// Use a small delay to ensure elements are fully rendered
// 			setTimeout(function () {
// 				if (
// 					window.GradientAPI &&
// 					typeof window.GradientAPI.handleAjaxLoadedContent === "function"
// 				) {
// 					window.GradientAPI.handleAjaxLoadedContent();
// 				}
// 			}, 100);
// 		}
// 	});

// 	observer.observe(document.body, {
// 		childList: true,
// 		subtree: true,
// 	});
// }
