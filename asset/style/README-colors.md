# Global Color System Documentation

## Overview
This document explains the global color system implemented for the Demo Site. All colors are defined as CSS custom properties (variables) in `asset/style/global-colors.css` and can be used throughout the entire site.

## Color Variables

### Primary Color Palette
```css
--light-green: #E3F2AA
--dark-green: #9fbe1b
--dark-grey: #333333
--light-blue: #CEE5EF
--dark-blue: #2C539F
```

### Pricing Colors
```css
--mrp-light-blue: #cde3ef    /* For MRP pricing buttons */
--pay-light-green: #E3F2AA   /* For PAY pricing buttons */
```

### Utility Colors
```css
--white: #ffffff
--black: #000000
--light-gray: #f3f4f6
--medium-gray: #d1d5db
--dark-gray: #4b5563
```

### Status Colors
```css
--success: #22c55e
--warning: #f59e0b
--error: #ef4444
--info: #3b82f6
```

## Usage Examples

### Using CSS Variables
```css
.my-element {
    background-color: var(--light-green);
    color: var(--dark-blue);
    border: 1px solid var(--light-blue);
}
```

### Using Utility Classes
```css
<!-- Background colors -->
<div class="bg-light-green">Light green background</div>
<div class="bg-dark-green">Dark green background</div>
<div class="bg-light-blue">Light blue background</div>
<div class="bg-dark-blue">Dark blue background</div>

<!-- Text colors -->
<span class="text-dark-grey">Dark grey text</span>
<span class="text-dark-blue">Dark blue text</span>

<!-- Border colors -->
<div class="border-light-green">Light green border</div>
```

## Section-Specific Colors

### Products Offering Discount Section
- **Background**: `var(--light-blue)` (#CEE5EF)
- **Title**: `var(--dark-blue)` (#2C539F)
- **Discount Banner**: `var(--dark-blue)` (#2C539F)
- **Navigation Arrows**: Blue arrow icons from `asset/icons/blue_arrow.png`
- **Product Cards**: `var(--light-blue)` background (same as section)
- **Product Card Borders**: `var(--light-blue)` (same as card background)
- **Product Image Area**: `var(--light-blue)` background
- **Product Details Area**: `var(--light-blue)` background
- **MRP Button**: `var(--mrp-light-blue)` (#cde3ef) background, `var(--dark-blue)` text
- **PAY Button**: `var(--pay-light-green)` (#E3F2AA) background, `var(--dark-grey)` text
- **Add to Cart Button**: `var(--dark-blue)` (#2C539F) background, white text
- **View All Button**: White background, `var(--dark-grey)` text

### Top 100 Products with Higher Discounts Section
- **Background**: `var(--light-green)` (#E3F2AA)
- **Title**: `var(--dark-grey)` (#333333)
- **Discount Banner**: `var(--dark-green)` (#9fbe1b)
- **Navigation Arrows**: Green arrow icons from `asset/icons/green_arrow.png`
- **Product Cards**: `var(--light-green)` background (same as section)
- **Product Card Borders**: `var(--light-green)` (same as card background)
- **Product Image Area**: `var(--light-green)` background
- **Product Details Area**: `var(--light-green)` background
- **MRP Button**: `var(--mrp-light-blue)` (#cde3ef) background, `var(--dark-blue)` text
- **PAY Button**: `var(--pay-light-green)` (#E3F2AA) background, `var(--dark-grey)` text
- **Add to Cart Button**: `var(--dark-grey)` (#333333) background, white text
- **View All Button**: `var(--light-gray)` (#f3f4f6) background, `var(--dark-grey)` text

## Icons

### Navigation Arrows
- **Blue Section**: `asset/icons/blue_arrow.png`
  - Left arrow: `transform: rotate(180deg)`
  - Right arrow: No rotation
  - Size: 20px x 20px
  - Button: White circular background with light gray border
- **Green Section**: `asset/icons/green_arrow.png`
  - Left arrow: `transform: rotate(180deg)`
  - Right arrow: No rotation
  - Size: 20px x 20px
  - Button: White circular background with light gray border

### Wishlist Icons
- **Default State**: `asset/icons/heart_gray.png`
  - Size: 16px x 16px
  - Position: Top-right corner of product image
- **Active State**: `asset/icons/heart_pink.png`
  - Size: 16px x 16px
  - Triggered when checkbox is checked
  - Hover effect: Scale 1.1

## Implementation Notes

1. **CSS Variables**: All colors are defined as CSS custom properties for easy maintenance and consistency.

2. **Utility Classes**: Pre-defined utility classes are available for quick styling.

3. **Section-Specific Styling**: Each section has specific CSS rules that override the global styles to maintain visual distinction.

4. **Responsive Design**: Colors work across all screen sizes and are included in responsive breakpoints.

5. **Accessibility**: Color combinations meet WCAG contrast requirements for readability.

## Best Practices

1. **Always use variables**: Instead of hardcoding colors, use the CSS variables.
2. **Consistent naming**: Use the established naming convention for new colors.
3. **Test contrast**: Ensure text remains readable on colored backgrounds.
4. **Document changes**: Update this file when adding new colors or changing existing ones.

## File Structure
```
asset/style/
├── global-colors.css      # Main color system file
├── README-colors.md       # This documentation
├── style.css             # Main stylesheet
└── product-card.css      # Product card specific styles
```
