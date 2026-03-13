from PIL import Image, ImageDraw, ImageFont
import os

# Define sizes
sizes = {
    'favicon-16x16.png': (16, 16),
    'favicon-32x32.png': (32, 32),
    'apple-touch-icon.png': (180, 180),
    'android-chrome-192x192.png': (192, 192),
    'android-chrome-512x512.png': (512, 512)
}

# Colors
color_start = "#06b6d4" # Cyan 500
color_end = "#9333ea"   # Purple 600

def create_gradient(width, height, start_color, end_color):
    base = Image.new('RGBA', (width, height), start_color)
    top = Image.new('RGBA', (width, height), end_color)
    mask = Image.new('L', (width, height))
    mask_data = []
    
    # Calculate gradient mask (diagonal)
    for y in range(height):
        for x in range(width):
            # Normalize to 0-255
            val = int(255 * (x + y) / (width + height))
            mask_data.append(val)
            
    mask.putdata(mask_data)
    base.paste(top, (0, 0), mask)
    return base

def get_font(size):
    # Try common Windows font paths
    font_paths = [
        "arialbd.ttf",
        "C:/Windows/Fonts/arialbd.ttf",
        "arial.ttf",
        "C:/Windows/Fonts/arial.ttf",
        "/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf", # Linux fallback
        "/usr/share/fonts/truetype/liberation/LiberationSans-Bold.ttf"
    ]
    
    for path in font_paths:
        try:
            return ImageFont.truetype(path, size)
        except IOError:
            continue
            
    return ImageFont.load_default()

def create_logo(size_tuple):
    width, height = size_tuple
    
    # 1. Background Gradient
    img = create_gradient(width, height, color_start, color_end)
    
    # 2. Rounded Corners Mask
    mask = Image.new('L', (width, height), 0)
    draw_mask = ImageDraw.Draw(mask)
    # Radius ~20% of width
    radius = int(width * 0.22)
    draw_mask.rounded_rectangle([(0, 0), (width, height)], radius=radius, fill=255)
    
    # 3. Apply Mask
    output = Image.new('RGBA', (width, height), (0, 0, 0, 0))
    output.paste(img, (0, 0), mask)
    
    # 4. Draw Text "A"
    draw = ImageDraw.Draw(output)
    
    # Font size ~60% of height
    font_size = int(height * 0.6)
    font = get_font(font_size)
    
    text = "A"
    
    # Calculate text position to center it
    try:
        # Pillow >= 9.2.0
        left, top, right, bottom = draw.textbbox((0, 0), text, font=font)
        text_width = right - left
        text_height = bottom - top
        # Adjust for baseline
        x = (width - text_width) / 2 - left
        y = (height - text_height) / 2 - top
    except AttributeError:
        # Older Pillow
        text_width, text_height = draw.textsize(text, font=font)
        x = (width - text_width) / 2
        y = (height - text_height) / 2
    
    # Draw text with slight shadow for better visibility?
    # No, flat white is cleaner on gradient.
    draw.text((x, y), text, font=font, fill="white")
    
    return output

# Main execution
output_dir = "public/img/favicons"
if not os.path.exists(output_dir):
    os.makedirs(output_dir)

print(f"Generating favicons in {output_dir}...")

# Base image (High Res)
base_size = (512, 512)
base_img = create_logo(base_size)

# Save base image
base_img.save(os.path.join(output_dir, "android-chrome-512x512.png"))

# Generate resized PNGs
for name, size in sizes.items():
    if name == 'android-chrome-512x512.png':
        continue
    
    # Resize using LANCZOS for quality
    resized = base_img.resize(size, resample=Image.Resampling.LANCZOS)
    resized.save(os.path.join(output_dir, name))

# Generate ICO (multi-size)
ico_sizes = [(16, 16), (32, 32), (48, 48), (64, 64)]
ico_imgs = []
for size in ico_sizes:
    ico_imgs.append(base_img.resize(size, resample=Image.Resampling.LANCZOS))

ico_path = os.path.join(output_dir, "favicon.ico")
ico_imgs[0].save(ico_path, format='ICO', sizes=[(i.width, i.height) for i in ico_imgs], append_images=ico_imgs[1:])

# Also save favicon.ico to public/ root
root_ico_path = "public/favicon.ico"
ico_imgs[0].save(root_ico_path, format='ICO', sizes=[(i.width, i.height) for i in ico_imgs], append_images=ico_imgs[1:])

print("Done.")
