#!/bin/bash

# Test script to verify opening hours display correctly on Porsgrunn page

echo "🔍 Testing Autostrada Porsgrunn opening hours..."
echo ""

# Fetch the page and search for Saturday hours
echo "Checking Tooltip (ID 10198):"
ssh kinsta-autostrada "cd public && wp post get 10198 --field=post_content" | grep -A2 "Lørdag"
echo ""

echo "Checking Bricks Template (ID 18686):"
ssh kinsta-autostrada "cd public && wp post meta get 18686 _bricks_page_content_2" | grep -E "11.00.*14.00" | head -1
echo ""

echo "Checking Code Snippet (ID 7) - Saturday value:"
ssh kinsta-autostrada "cd public && wp db query \"SELECT code FROM wp_snippets WHERE id = 7;\"" | grep -i "'saturday'" -A1
echo ""

echo "✅ All three locations should show: 11.00 - 14.00 (or 11:00 - 14:00)"
