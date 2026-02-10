# Gender Distribution Doughnut Chart Implementation

## Plan
1. Replace the text-based gender distribution display with a Chart.js doughnut chart
2. Keep the existing `<x-data-card>` wrapper structure
3. Add canvas element for the chart
4. Add JavaScript initialization for the doughnut chart
5. Include percentage display in tooltips

## Implementation Details
- File to edit: `resources/views/dashboard/index.blade.php`
- Section: Gender Distribution (lines 66-86)
- Chart type: doughnut
- Colors: Purple (#6366F1) for male, Pink (#EC4899) for female, Yellow (#FBBF24) for other/unknown
- Labels: Capitalized gender names

## Status
- [x] Edit dashboard/index.blade.php to replace text display with doughnut chart
- [x] Replace Top Nationalities list with Bar Chart
- [ ] Test the implementation

