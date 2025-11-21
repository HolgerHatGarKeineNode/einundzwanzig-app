# Livewire Flux Component Guidelines
## Usage Pattern
Components use the format: `<flux:name />`
## Component Reference
### Layout & Structure
- **flux:card** - Basic container with default slot
- **flux:field** - Form field wrapper with label/description support
- **flux:brand** - Logo/company name display with href navigation

### Navigation
- **flux:breadcrumbs** - Navigation breadcrumbs
    - **flux:breadcrumbs.item** - Individual breadcrumb with href/icon

- **flux:accordion** - Collapsible content sections
    - **flux:accordion.item** - Individual accordion item with heading/content
    - **flux:accordion.heading** - Accordion header
    - **flux:accordion.content** - Accordion body

### Form Controls
- **flux:input** - Text input with wire:model, validation, icons, masks
- **flux:select** - Select input
- **flux:select.option** - Select options
- **flux:autocomplete** - Searchable input with dropdown items
- **flux:checkbox** - Single checkbox or grouped checkboxes
- **flux:date-picker** - Date selection with calendar, ranges, presets
- **flux:editor** - Rich text editor with toolbar

### Interactive Elements
- **flux:button** - Button with variants (primary, outline, danger), icons, loading states
- **flux:dropdown** - Dropdown menu with positioning options
- **flux:menu** - Complex menu with items, submenus, separators, checkboxes, radio buttons
- **flux:command** - Command palette with searchable items
- **flux:context** - Right-click context menu wrapper

### Display Components
- **flux:avatar** - User avatar with initials, images, badges, grouping
- **flux:badge** - Status/label badges with colors and variants
- **flux:callout** - Highlighted information blocks with icons and actions
- **flux:calendar** - Calendar display with date selection modes
- **flux:chart** - Data visualization with lines, areas, axes, tooltips

### Key Props
- **wire:model** - Livewire property binding
- **variant** - Visual style options (outline, primary, filled, etc.)
- **size** - Component sizing (xs, sm, base, lg, xl, 2xl)
- **disabled/invalid** - State management
- **icon/icon:trailing** - Icon placement with variants
- **label/description** - Form field labeling
- **color** - Color theming options

### Common Patterns
- Most form components support wire:model binding
- Many components have label/description props for field wrapping
- Icon components accept variant options (outline, solid, mini, micro)
- Size props typically offer xs, sm, base, lg, xl, 2xl options
- Variant props provide visual style alternatives
