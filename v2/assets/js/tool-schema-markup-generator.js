/**
 * Schema Markup Generator — Tool JS
 * JSON-LD structured data builder for SEO.
 *
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var fieldsContainer = document.getElementById('tc-schema-fields-container');
    if (!fieldsContainer) return;

    var codeOutput = document.getElementById('tc-schema-code');
    var currentType = 'article';

    // ── Schema type fields ───────────────────────────────────

    var schemaFields = {
        'article': [
            { id: 'headline', label: 'Headline', placeholder: 'Article Title', required: true },
            { id: 'description', label: 'Description', placeholder: 'Article description', type: 'textarea' },
            { id: 'author', label: 'Author Name', placeholder: 'Author Name', required: true },
            { id: 'url', label: 'Article URL', placeholder: 'https://example.com/article' },
            { id: 'image', label: 'Image URL', placeholder: 'https://example.com/image.jpg' },
            { id: 'datePublished', label: 'Publish Date', placeholder: '2025-01-15', type: 'date' },
            { id: 'dateModified', label: 'Modified Date', placeholder: '2025-01-20', type: 'date' },
            { id: 'publisher', label: 'Publisher Name', placeholder: 'Publisher Name' },
            { id: 'publisherLogo', label: 'Publisher Logo URL', placeholder: 'https://example.com/logo.png' }
        ],
        'product': [
            { id: 'name', label: 'Product Name', placeholder: 'Product Name', required: true },
            { id: 'description', label: 'Description', placeholder: 'Product description', type: 'textarea' },
            { id: 'image', label: 'Image URL', placeholder: 'https://example.com/product.jpg' },
            { id: 'url', label: 'Product URL', placeholder: 'https://example.com/product' },
            { id: 'price', label: 'Price', placeholder: '29.99', required: true },
            { id: 'priceCurrency', label: 'Currency', placeholder: 'USD', default: 'USD' },
            { id: 'brand', label: 'Brand', placeholder: 'Brand Name' },
            { id: 'sku', label: 'SKU', placeholder: 'ABC-123' },
            { id: 'availability', label: 'Availability', type: 'select', options: ['InStock', 'OutOfStock', 'PreOrder', 'Discontinued'], default: 'InStock' },
            { id: 'ratingValue', label: 'Rating (1-5)', placeholder: '4.5' },
            { id: 'reviewCount', label: 'Review Count', placeholder: '128' }
        ],
        'local-business': [
            { id: 'name', label: 'Business Name', placeholder: 'Business Name', required: true },
            { id: 'description', label: 'Description', placeholder: 'Business description', type: 'textarea' },
            { id: 'url', label: 'Website URL', placeholder: 'https://example.com' },
            { id: 'telephone', label: 'Phone', placeholder: '+1-555-123-4567' },
            { id: 'email', label: 'Email', placeholder: 'info@example.com' },
            { id: 'address', label: 'Street Address', placeholder: '123 Main St' },
            { id: 'addressLocality', label: 'City', placeholder: 'New York' },
            { id: 'addressRegion', label: 'State', placeholder: 'NY' },
            { id: 'postalCode', label: 'Postal Code', placeholder: '10001' },
            { id: 'addressCountry', label: 'Country Code', placeholder: 'US' },
            { id: 'openingHours', label: 'Opening Hours', placeholder: 'Mo-Fr 09:00-17:00' },
            { id: 'image', label: 'Logo URL', placeholder: 'https://example.com/logo.png' }
        ],
        'faq': [
            { id: 'q1', label: 'Question 1', placeholder: 'What is...?', required: true },
            { id: 'a1', label: 'Answer 1', placeholder: 'Answer...', type: 'textarea', required: true },
            { id: 'q2', label: 'Question 2', placeholder: 'How do I...?' },
            { id: 'a2', label: 'Answer 2', placeholder: 'Answer...', type: 'textarea' },
            { id: 'q3', label: 'Question 3', placeholder: 'Why does...?' },
            { id: 'a3', label: 'Answer 3', placeholder: 'Answer...', type: 'textarea' },
            { id: 'q4', label: 'Question 4', placeholder: 'Can I...?' },
            { id: 'a4', label: 'Answer 4', placeholder: 'Answer...', type: 'textarea' }
        ],
        'howto': [
            { id: 'name', label: 'Title', placeholder: 'How to do something', required: true },
            { id: 'description', label: 'Description', placeholder: 'Brief description', type: 'textarea' },
            { id: 'image', label: 'Image URL', placeholder: 'https://example.com/image.jpg' },
            { id: 'step1', label: 'Step 1', placeholder: 'First step description', required: true },
            { id: 'step2', label: 'Step 2', placeholder: 'Second step description' },
            { id: 'step3', label: 'Step 3', placeholder: 'Third step description' },
            { id: 'step4', label: 'Step 4', placeholder: 'Fourth step description' },
            { id: 'step5', label: 'Step 5', placeholder: 'Fifth step description' },
            { id: 'totalTime', label: 'Total Time (ISO 8601)', placeholder: 'PT30M' }
        ],
        'event': [
            { id: 'name', label: 'Event Name', placeholder: 'Event Name', required: true },
            { id: 'description', label: 'Description', placeholder: 'Event description', type: 'textarea' },
            { id: 'url', label: 'Event URL', placeholder: 'https://example.com/event' },
            { id: 'image', label: 'Image URL', placeholder: 'https://example.com/event.jpg' },
            { id: 'startDate', label: 'Start Date', placeholder: '2025-06-15T18:00', type: 'datetime-local', required: true },
            { id: 'endDate', label: 'End Date', placeholder: '2025-06-15T22:00', type: 'datetime-local' },
            { id: 'location', label: 'Venue Name', placeholder: 'Venue Name' },
            { id: 'address', label: 'Address', placeholder: '123 Main St, City, State' },
            { id: 'price', label: 'Price', placeholder: '25.00' },
            { id: 'priceCurrency', label: 'Currency', placeholder: 'USD', default: 'USD' }
        ],
        'recipe': [
            { id: 'name', label: 'Recipe Name', placeholder: 'Recipe Name', required: true },
            { id: 'description', label: 'Description', placeholder: 'Recipe description', type: 'textarea' },
            { id: 'image', label: 'Image URL', placeholder: 'https://example.com/recipe.jpg' },
            { id: 'author', label: 'Author', placeholder: 'Chef Name' },
            { id: 'prepTime', label: 'Prep Time', placeholder: 'PT15M' },
            { id: 'cookTime', label: 'Cook Time', placeholder: 'PT30M' },
            { id: 'totalTime', label: 'Total Time', placeholder: 'PT45M' },
            { id: 'recipeYield', label: 'Servings', placeholder: '4' },
            { id: 'recipeCategory', label: 'Category', placeholder: 'Dessert' },
            { id: 'recipeCuisine', label: 'Cuisine', placeholder: 'Italian' },
            { id: 'calories', label: 'Calories', placeholder: '350' },
            { id: 'ingredients', label: 'Ingredients (one per line)', placeholder: '2 cups flour\n1 cup sugar\n3 eggs', type: 'textarea' }
        ],
        'organization': [
            { id: 'name', label: 'Organization Name', placeholder: 'Company Name', required: true },
            { id: 'description', label: 'Description', placeholder: 'About the organization', type: 'textarea' },
            { id: 'url', label: 'Website URL', placeholder: 'https://example.com' },
            { id: 'logo', label: 'Logo URL', placeholder: 'https://example.com/logo.png' },
            { id: 'email', label: 'Email', placeholder: 'info@example.com' },
            { id: 'telephone', label: 'Phone', placeholder: '+1-555-123-4567' },
            { id: 'address', label: 'Address', placeholder: '123 Main St, City, State' },
            { id: 'foundingDate', label: 'Founded', placeholder: '2020' }
        ],
        'breadcrumb': [
            { id: 'name1', label: 'Level 1 Name', placeholder: 'Home', required: true },
            { id: 'url1', label: 'Level 1 URL', placeholder: 'https://example.com', required: true },
            { id: 'name2', label: 'Level 2 Name', placeholder: 'Products' },
            { id: 'url2', label: 'Level 2 URL', placeholder: 'https://example.com/products' },
            { id: 'name3', label: 'Level 3 Name', placeholder: 'Category' },
            { id: 'url3', label: 'Level 3 URL', placeholder: 'https://example.com/products/category' },
            { id: 'name4', label: 'Level 4 Name', placeholder: 'Current Page' },
            { id: 'url4', label: 'Level 4 URL', placeholder: 'https://example.com/products/category/item' }
        ]
    };

    // ── Render fields ────────────────────────────────────────

    function renderFields(type) {
        var fields = schemaFields[type];
        if (!fields) { fieldsContainer.innerHTML = ''; return; }

        var html = '';
        fields.forEach(function (f) {
            var val = f.default || '';
            html += '<div class="tc-input-group">';
            html += '<label class="tc-label">' + f.label + (f.required ? ' *' : '') + '</label>';
            if (f.type === 'textarea') {
                html += '<textarea class="tc-input tc-schema-field" data-id="' + f.id + '" rows="2" placeholder="' + (f.placeholder || '') + '">' + val + '</textarea>';
            } else if (f.type === 'select') {
                html += '<select class="tc-input tc-schema-field" data-id="' + f.id + '">';
                f.options.forEach(function (opt) {
                    html += '<option value="' + opt + '"' + (opt === val ? ' selected' : '') + '>' + opt + '</option>';
                });
                html += '</select>';
            } else if (f.type === 'date' || f.type === 'datetime-local') {
                html += '<input type="' + f.type + '" class="tc-input tc-schema-field" data-id="' + f.id + '" value="' + val + '" placeholder="' + (f.placeholder || '') + '">';
            } else {
                html += '<input type="text" class="tc-input tc-schema-field" data-id="' + f.id + '" value="' + val + '" placeholder="' + (f.placeholder || '') + '">';
            }
            html += '</div>';
        });

        fieldsContainer.innerHTML = html;

        // Bind events
        fieldsContainer.querySelectorAll('.tc-schema-field').forEach(function (el) {
            el.addEventListener('input', generateSchema);
            el.addEventListener('change', generateSchema);
        });

        generateSchema();
    }

    // ── Generate schema ──────────────────────────────────────

    function getVal(id) {
        var el = fieldsContainer.querySelector('[data-id="' + id + '"]');
        return el ? el.value.trim() : '';
    }

    function generateSchema() {
        var schema = { '@context': 'https://schema.org' };

        switch (currentType) {
            case 'article':
                schema['@type'] = 'Article';
                schema.headline = getVal('headline');
                schema.description = getVal('description');
                schema.url = getVal('url');
                schema.image = getVal('image');
                schema.datePublished = getVal('datePublished');
                schema.dateModified = getVal('dateModified');
                if (getVal('author')) schema.author = { '@type': 'Person', name: getVal('author') };
                if (getVal('publisher')) schema.publisher = { '@type': 'Organization', name: getVal('publisher') };
                if (getVal('publisherLogo')) {
                    if (!schema.publisher) schema.publisher = { '@type': 'Organization' };
                    schema.publisher.logo = { '@type': 'ImageObject', url: getVal('publisherLogo') };
                }
                break;

            case 'product':
                schema['@type'] = 'Product';
                schema.name = getVal('name');
                schema.description = getVal('description');
                schema.image = getVal('image');
                schema.url = getVal('url');
                schema.brand = getVal('brand') ? { '@type': 'Brand', name: getVal('brand') } : undefined;
                schema.sku = getVal('sku');
                if (getVal('price')) {
                    schema.offers = {
                        '@type': 'Offer',
                        price: getVal('price'),
                        priceCurrency: getVal('priceCurrency') || 'USD',
                        availability: 'https://schema.org/' + (getVal('availability') || 'InStock')
                    };
                }
                if (getVal('ratingValue')) {
                    schema.aggregateRating = {
                        '@type': 'AggregateRating',
                        ratingValue: getVal('ratingValue'),
                        reviewCount: getVal('reviewCount') || '1'
                    };
                }
                break;

            case 'local-business':
                schema['@type'] = 'LocalBusiness';
                schema.name = getVal('name');
                schema.description = getVal('description');
                schema.url = getVal('url');
                schema.telephone = getVal('telephone');
                schema.email = getVal('email');
                schema.openingHours = getVal('openingHours');
                schema.image = getVal('image');
                if (getVal('address') || getVal('addressLocality')) {
                    schema.address = {
                        '@type': 'PostalAddress',
                        streetAddress: getVal('address'),
                        addressLocality: getVal('addressLocality'),
                        addressRegion: getVal('addressRegion'),
                        postalCode: getVal('postalCode'),
                        addressCountry: getVal('addressCountry')
                    };
                }
                break;

            case 'faq':
                schema['@type'] = 'FAQPage';
                schema.mainEntity = [];
                for (var i = 1; i <= 4; i++) {
                    var q = getVal('q' + i);
                    var a = getVal('a' + i);
                    if (q && a) {
                        schema.mainEntity.push({
                            '@type': 'Question',
                            name: q,
                            acceptedAnswer: { '@type': 'Answer', text: a }
                        });
                    }
                }
                break;

            case 'howto':
                schema['@type'] = 'HowTo';
                schema.name = getVal('name');
                schema.description = getVal('description');
                schema.image = getVal('image');
                schema.totalTime = getVal('totalTime');
                schema.step = [];
                for (var s = 1; s <= 5; s++) {
                    var stepText = getVal('step' + s);
                    if (stepText) {
                        schema.step.push({ '@type': 'HowToStep', text: stepText });
                    }
                }
                break;

            case 'event':
                schema['@type'] = 'Event';
                schema.name = getVal('name');
                schema.description = getVal('description');
                schema.url = getVal('url');
                schema.image = getVal('image');
                schema.startDate = getVal('startDate');
                schema.endDate = getVal('endDate');
                if (getVal('location')) {
                    schema.location = { '@type': 'Place', name: getVal('location') };
                    if (getVal('address')) schema.location.address = getVal('address');
                }
                if (getVal('price')) {
                    schema.offers = {
                        '@type': 'Offer',
                        price: getVal('price'),
                        priceCurrency: getVal('priceCurrency') || 'USD'
                    };
                }
                break;

            case 'recipe':
                schema['@type'] = 'Recipe';
                schema.name = getVal('name');
                schema.description = getVal('description');
                schema.image = getVal('image');
                schema.author = getVal('author') ? { '@type': 'Person', name: getVal('author') } : undefined;
                schema.prepTime = getVal('prepTime');
                schema.cookTime = getVal('cookTime');
                schema.totalTime = getVal('totalTime');
                schema.recipeYield = getVal('recipeYield');
                schema.recipeCategory = getVal('recipeCategory');
                schema.recipeCuisine = getVal('recipeCuisine');
                if (getVal('calories')) schema.nutrition = { '@type': 'NutritionInformation', calories: getVal('calories') + ' calories' };
                if (getVal('ingredients')) {
                    schema.recipeIngredient = getVal('ingredients').split('\n').filter(function (l) { return l.trim(); });
                }
                break;

            case 'organization':
                schema['@type'] = 'Organization';
                schema.name = getVal('name');
                schema.description = getVal('description');
                schema.url = getVal('url');
                schema.logo = getVal('logo');
                schema.email = getVal('email');
                schema.telephone = getVal('telephone');
                schema.foundingDate = getVal('foundingDate');
                if (getVal('address')) schema.address = getVal('address');
                break;

            case 'breadcrumb':
                schema['@type'] = 'BreadcrumbList';
                schema.itemListElement = [];
                for (var b = 1; b <= 4; b++) {
                    var bName = getVal('name' + b);
                    var bUrl = getVal('url' + b);
                    if (bName && bUrl) {
                        schema.itemListElement.push({
                            '@type': 'ListItem',
                            position: b,
                            name: bName,
                            item: bUrl
                        });
                    }
                }
                break;
        }

        // Clean undefined
        var clean = JSON.parse(JSON.stringify(schema));
        codeOutput.value = JSON.stringify(clean, null, 2);
    }

    // ── Type cards ───────────────────────────────────────────

    document.querySelectorAll('.tc-schema-type-cards .tc-rsz-mode-card').forEach(function (card) {
        card.addEventListener('click', function () {
            document.querySelectorAll('.tc-schema-type-cards .tc-rsz-mode-card').forEach(function (c) { c.classList.remove('sel'); });
            card.classList.add('sel');
            currentType = card.getAttribute('data-val') || 'article';
            renderFields(currentType);
        });
    });

    // ── Copy ─────────────────────────────────────────────────

    document.getElementById('tc-schema-copy').addEventListener('click', function () {
        TCTP.copyText(codeOutput.value);
        TCTP.toast('JSON-LD copied!', '\u2705');
    });

    // ── Init ─────────────────────────────────────────────────

    renderFields('article');
})();
