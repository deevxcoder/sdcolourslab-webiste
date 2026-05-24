import 'package:flutter/material.dart';
import 'package:lab_desktop_app/services/api_service.dart';

class BroadcastScreen extends StatefulWidget {
  const BroadcastScreen({super.key});

  @override
  State<BroadcastScreen> createState() => _BroadcastScreenState();
}

class _BroadcastScreenState extends State<BroadcastScreen> {
  final _formKey = GlobalKey<FormState>();
  final _titleController = TextEditingController();
  final _messageController = TextEditingController();
  String _selectedType = 'info';
  bool _isSending = false;

  final ApiService _apiService = ApiService();

  Future<void> _sendBroadcast() async {
    if (!_formKey.currentState!.validate()) return;

    setState(() => _isSending = true);

    final result = await _apiService.post('/admin/broadcast', {
      'title': _titleController.text,
      'message': _messageController.text,
      'type': _selectedType,
    });

    setState(() => _isSending = false);

    if (result['success'] == true) {
      _titleController.clear();
      _messageController.clear();
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Announcement broadcasted successfully!'), backgroundColor: Colors.green),
        );
      }
    } else {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text(result['message'] ?? 'Failed to send broadcast'), backgroundColor: Colors.redAccent),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.all(32.0),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text(
            'Broadcast Center',
            style: TextStyle(fontSize: 28, fontWeight: FontWeight.bold, color: Colors.white),
          ),
          const Text(
            'Send instant announcements to all approved photographers.',
            style: TextStyle(color: Colors.grey),
          ),
          const SizedBox(height: 32),
          Expanded(
            child: Row(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                // Form
                Expanded(
                  flex: 3,
                  child: Container(
                    padding: const EdgeInsets.all(32),
                    decoration: BoxDecoration(
                      color: const Color(0xFF1A1D27),
                      borderRadius: BorderRadius.circular(16),
                      border: Border.all(color: Colors.white.withOpacity(0.05)),
                    ),
                    child: Form(
                      key: _formKey,
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          const Text('NEW ANNOUNCEMENT', style: TextStyle(color: Color(0xFFC9A227), fontWeight: FontWeight.bold, fontSize: 12, letterSpacing: 1.2)),
                          const SizedBox(height: 24),
                          TextFormField(
                            controller: _titleController,
                            style: const TextStyle(color: Colors.white),
                            decoration: _inputDecoration('Short Title (e.g., Holiday Notice)'),
                            validator: (v) => v!.isEmpty ? 'Required' : null,
                          ),
                          const SizedBox(height: 20),
                          DropdownButtonFormField<String>(
                            value: _selectedType,
                            dropdownColor: const Color(0xFF1A1D27),
                            style: const TextStyle(color: Colors.white),
                            decoration: _inputDecoration('Announcement Type'),
                            items: const [
                              DropdownMenuItem(value: 'info', child: Text('Information')),
                              DropdownMenuItem(value: 'offer', child: Text('Special Offer')),
                              DropdownMenuItem(value: 'warning', child: Text('Alert/Warning')),
                            ],
                            onChanged: (v) => setState(() => _selectedType = v!),
                          ),
                          const SizedBox(height: 20),
                          TextFormField(
                            controller: _messageController,
                            maxLines: 6,
                            style: const TextStyle(color: Colors.white),
                            decoration: _inputDecoration('Full Message Content...'),
                            validator: (v) => v!.isEmpty ? 'Required' : null,
                          ),
                          const SizedBox(height: 32),
                          SizedBox(
                            width: double.infinity,
                            height: 56,
                            child: ElevatedButton.icon(
                              onPressed: _isSending ? null : _sendBroadcast,
                              icon: _isSending ? const SizedBox(width: 18, height: 18, child: CircularProgressIndicator(strokeWidth: 2, color: Colors.black)) : const Icon(Icons.send_rounded),
                              label: Text(_isSending ? 'SENDING...' : 'BROADCAST TO EVERYONE'),
                              style: ElevatedButton.styleFrom(
                                backgroundColor: const Color(0xFFC9A227),
                                foregroundColor: Colors.black,
                                textStyle: const TextStyle(fontWeight: FontWeight.bold),
                                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                              ),
                            ),
                          ),
                        ],
                      ),
                    ),
                  ),
                ),
                const SizedBox(width: 32),
                // Preview / Tips
                Expanded(
                  flex: 2,
                  child: Column(
                    children: [
                      Container(
                        padding: const EdgeInsets.all(24),
                        decoration: BoxDecoration(
                          color: const Color(0xFFC9A227).withOpacity(0.05),
                          borderRadius: BorderRadius.circular(16),
                          border: Border.all(color: const Color(0xFFC9A227).withOpacity(0.2)),
                        ),
                        child: const Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Row(
                              children: [
                                Icon(Icons.lightbulb_outline, color: Color(0xFFC9A227), size: 18),
                                SizedBox(width: 12),
                                Text('Pro Tips', style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
                              ],
                            ),
                            SizedBox(height: 16),
                            Text(
                              '• Keep titles short and punchy.\n'
                              '• Use "Special Offer" for price drops.\n'
                              '• These appear instantly on photographer home screens.\n'
                              '• Announcements cannot be edited once sent.',
                              style: TextStyle(color: Colors.grey, fontSize: 13, height: 1.6),
                            ),
                          ],
                        ),
                      ),
                    ],
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  InputDecoration _inputDecoration(String label) {
    return InputDecoration(
      labelText: label,
      labelStyle: const TextStyle(color: Colors.grey),
      filled: true,
      fillColor: Colors.white.withOpacity(0.02),
      border: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: BorderSide(color: Colors.white.withOpacity(0.1))),
      enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: BorderSide(color: Colors.white.withOpacity(0.1))),
      focusedBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: const BorderSide(color: Color(0xFFC9A227))),
    );
  }
}
