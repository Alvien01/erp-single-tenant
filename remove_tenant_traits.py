import os

model_dir = r"d:\Code\erp-single-tenant\app\Models"

for root, _, files in os.walk(model_dir):
    for file in files:
        if file.endswith(".php"):
            filepath = os.path.join(root, file)
            try:
                with open(filepath, "r", encoding="utf-8") as f:
                    content = f.read()
            except Exception as e:
                print(f"Error reading {file}: {e}")
                continue
            
            if "BelongsToTenant" in content:
                lines = content.split('\n')
                new_lines = []
                for line in lines:
                    if 'use App\\Traits\\BelongsToTenant;' in line:
                        continue
                    # Match use BelongsToTenant; inside class
                    if line.strip() == 'use BelongsToTenant;':
                        continue
                    new_lines.append(line)
                
                with open(filepath, "w", encoding="utf-8") as f:
                    f.write('\n'.join(new_lines))
                print(f"Removed BelongsToTenant from {file}")
