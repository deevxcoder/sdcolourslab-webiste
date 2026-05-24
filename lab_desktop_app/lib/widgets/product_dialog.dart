import 'package:flutter/material.dart';
import 'package:lab_desktop_app/models/product_model.dart';

class ProductDialog extends StatefulWidget {
  final Product? product;

  const ProductDialog({super.key, this.product});

  @override
  State<ProductDialog> createState() => _ProductDialogState();
}

class _ProductDialogState extends State<ProductDialog> {
  final _formKey = GlobalKey<FormState>();
  late TextEditingController _nameController;
  late TextEditingController _descController;
  late TextEditingController _priceController;
  late TextEditingController _unitController;
  late TextEditingController _minQtyController;
  String _category = 'Album';

  final List<String> _categories = ['Album', 'Print', 'Frame', 'Gift', 'Other'];

  @override
  void initState() {
    super.initState();
    _nameController = TextEditingController(text: widget.product?.name);
    _descController = TextEditingController(text: widget.product?.description);
    _priceController = TextEditingController(text: widget.product?.price.toString());
    _unitController = TextEditingController(text: widget.product?.unit ?? 'pcs');
    _minQtyController = TextEditingController(text: widget.product?.minQty.toString() ?? '1');
    if (widget.product != null) {
      _category = widget.product!.category;
    }
  }

  @override
  Widget build(BuildContext context) {
    return AlertDialog(
      backgroundColor: const Color(0xFF1A1D27),
      title: Text(
        widget.product == null ? 'Add New Product' : 'Edit Product',
        style: const TextStyle(color: Colors.white),
      ),
      content: SizedBox(
        width: 500,
        child: Form(
          key: _formKey,
          child: SingleChildScrollView(
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                _buildTextField(_nameController, 'Product Name', Icons.inventory_2_outlined),
                const SizedBox(height: 16),
                _buildDropdown(),
                const SizedBox(height: 16),
                Row(
                  children: [
                    Expanded(child: _buildTextField(_priceController, 'Price (INR)', Icons.payments_outlined, isNumber: true)),
                    const SizedBox(width: 16),
                    Expanded(child: _buildTextField(_unitController, 'Unit (e.g. pcs, sheet)', Icons.straighten_outlined)),
                  ],
                ),
                const SizedBox(height: 16),
                _buildTextField(_minQtyController, 'Min Order Qty', Icons.numbers_outlined, isNumber: true),
                const SizedBox(height: 16),
                _buildTextField(_descController, 'Description (Optional)', Icons.description_outlined, maxLines: 3),
              ],
            ),
          ),
        ),
      ),
      actions: [
        TextButton(
          onPressed: () => Navigator.pop(context),
          child: const Text('Cancel', style: TextStyle(color: Colors.grey)),
        ),
        ElevatedButton(
          onPressed: _submit,
          style: ElevatedButton.styleFrom(backgroundColor: const Color(0xFFC9A227), foregroundColor: Colors.black),
          child: Text(widget.product == null ? 'CREATE PRODUCT' : 'SAVE CHANGES'),
        ),
      ],
    );
  }

  Widget _buildTextField(TextEditingController controller, String label, IconData icon, {bool isNumber = false, int maxLines = 1}) {
    return TextFormField(
      controller: controller,
      maxLines: maxLines,
      style: const TextStyle(color: Colors.white),
      keyboardType: isNumber ? TextInputType.number : TextInputType.text,
      decoration: InputDecoration(
        labelText: label,
        labelStyle: const TextStyle(color: Colors.grey, fontSize: 13),
        prefixIcon: Icon(icon, color: const Color(0xFFC9A227), size: 20),
        filled: true,
        fillColor: Colors.white.withOpacity(0.02),
        border: OutlineInputBorder(borderRadius: BorderRadius.circular(8), borderSide: BorderSide.none),
      ),
      validator: (val) => val == null || val.isEmpty ? 'Required' : null,
    );
  }

  Widget _buildDropdown() {
    return DropdownButtonFormField<String>(
      value: _categories.contains(_category) ? _category : _categories.first,
      dropdownColor: const Color(0xFF1A1D27),
      decoration: InputDecoration(
        labelText: 'Category',
        labelStyle: const TextStyle(color: Colors.grey, fontSize: 13),
        prefixIcon: const Icon(Icons.category_outlined, color: Color(0xFFC9A227), size: 20),
        filled: true,
        fillColor: Colors.white.withOpacity(0.02),
        border: OutlineInputBorder(borderRadius: BorderRadius.circular(8), borderSide: BorderSide.none),
      ),
      items: _categories.map((c) => DropdownMenuItem(value: c, child: Text(c, style: const TextStyle(color: Colors.white)))).toList(),
      onChanged: (val) => setState(() => _category = val!),
    );
  }

  void _submit() {
    if (_formKey.currentState!.validate()) {
      final data = {
        'name': _nameController.text.trim(),
        'category': _category,
        'price': double.parse(_priceController.text),
        'unit': _unitController.text.trim(),
        'min_qty': int.parse(_minQtyController.text),
        'description': _descController.text.trim(),
      };
      Navigator.pop(context, data);
    }
  }
}
