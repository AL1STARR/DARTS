<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $title }} - DARTS</title>
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
  <style>
    body {
      background: var(--bg);
      padding: 40px 20px;
    }
    .policy-container {
      max-width: 900px;
      margin: 0 auto;
      background: white;
      padding: 50px;
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(0,0,0,.1);
    }
    .policy-container h1 {
      color: var(--navy);
      margin-bottom: 10px;
      font-size: 28px;
    }
    .policy-container .updated {
      color: var(--muted);
      font-size: 12px;
      margin-bottom: 30px;
    }
    .policy-container h2 {
      color: var(--navy);
      margin-top: 30px;
      margin-bottom: 15px;
      font-size: 18px;
    }
    .policy-container h3 {
      color: #2c3e50;
      margin-top: 20px;
      margin-bottom: 10px;
      font-size: 14px;
    }
    .policy-container p {
      line-height: 1.8;
      color: #555;
      margin-bottom: 15px;
      font-size: 14px;
    }
    .policy-container ul, .policy-container ol {
      margin-left: 20px;
      margin-bottom: 15px;
      line-height: 1.8;
    }
    .policy-container li {
      margin-bottom: 8px;
      color: #555;
      font-size: 14px;
    }
    .back-link {
      display: inline-block;
      margin-top: 40px;
      color: var(--blue);
      text-decoration: none;
      font-weight: 600;
    }
    .back-link:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="policy-container">
    @if($page === 'privacy')
      <h1>Privacy Policy</h1>
      <div class="updated">Last updated: {{ date('F j, Y') }}</div>
      
      <h2>1. Introduction</h2>
      <p>DARTS Intelligence ("Company", "we", "our", or "us") operates the DARTS document management system. This page informs you of our policies regarding the collection, use, and disclosure of personal data when you use our Service and the choices you have associated with that data.</p>
      
      <h2>2. Information Collection and Use</h2>
      <h3>We collect several different types of information for various purposes to provide and improve our Service to you:</h3>
      <ul>
        <li><strong>Personal Data:</strong> While using our Service, we may ask you to provide us with certain personally identifiable information that can be used to contact or identify you ("Personal Data"). This may include:
          <ul>
            <li>Email address</li>
            <li>First name and last name</li>
            <li>Department and role information</li>
            <li>Phone number (optional)</li>
            <li>Cookies and usage data</li>
          </ul>
        </li>
      </ul>
      
      <h2>3. Use of Data</h2>
      <p>DARTS Intelligence uses the collected data for various purposes:</p>
      <ul>
        <li>To provide and maintain our Service</li>
        <li>To notify you about changes to our Service</li>
        <li>To allow you to participate in interactive features of our Service when you choose to do so</li>
        <li>To provide customer support</li>
        <li>To gather analysis or valuable information so that we can improve our Service</li>
        <li>To monitor the usage of our Service</li>
        <li>To detect, prevent and address technical issues</li>
      </ul>
      
      <h2>4. Security of Data</h2>
      <p>The security of your data is important to us, but remember that no method of transmission over the Internet or method of electronic storage is 100% secure. While we strive to use commercially acceptable means to protect your Personal Data, we cannot guarantee its absolute security.</p>
      
      <h2>5. Changes to This Privacy Policy</h2>
      <p>We may update our Privacy Policy from time to time. We will notify you of any changes by posting the new Privacy Policy on this page and updating the "Last updated" date at the top of this Privacy Policy.</p>
      
      <h2>6. Contact Us</h2>
      <p>If you have any questions about this Privacy Policy, please contact us at: admin@darts-intelligence.local</p>

    @elseif($page === 'terms')
      <h1>Terms of Service</h1>
      <div class="updated">Last updated: {{ date('F j, Y') }}</div>
      
      <h2>1. Agreement to Terms</h2>
      <p>By accessing and using the DARTS (Document Archiving and Release Tracking System) platform ("Service"), you accept and agree to be bound by the terms and provision of this agreement. If you do not agree to abide by the above, please do not use this service.</p>
      
      <h2>2. Use License</h2>
      <p>Permission is granted to temporarily download one copy of the materials (information or software) on DARTS for personal, non-commercial transitory viewing only. This is the grant of a license, not a transfer of title, and under this license you may not:</p>
      <ul>
        <li>Modify or copy the materials</li>
        <li>Use the materials for any commercial purpose or for any public display</li>
        <li>Attempt to decompile or reverse engineer any software contained on DARTS</li>
        <li>Remove any copyright or other proprietary notations from the materials</li>
        <li>Transfer the materials to another person or "mirror" the materials on any other server</li>
      </ul>
      
      <h2>3. Disclaimer</h2>
      <p>The materials on DARTS's website are provided on an 'as is' basis. DARTS makes no warranties, expressed or implied, and hereby disclaims and negates all other warranties including, without limitation, implied warranties or conditions of merchantability, fitness for a particular purpose, or non-infringement of intellectual property or other violation of rights.</p>
      
      <h2>4. Limitations</h2>
      <p>In no event shall DARTS or its suppliers be liable for any damages (including, without limitation, damages for loss of data or profit, or due to business interruption) arising out of the use or inability to use the materials on DARTS, even if DARTS or a DARTS authorized representative has been notified orally or in writing of the possibility of such damage.</p>
      
      <h2>5. Accuracy of Materials</h2>
      <p>The materials appearing on DARTS could include technical, typographical, or photographic errors. DARTS does not warrant that any of the materials on its website are accurate, complete, or current. DARTS may make changes to the materials contained on its website at any time without notice.</p>
      
      <h2>6. Links</h2>
      <p>DARTS has not reviewed all of the sites linked to its website and is not responsible for the contents of any such linked site. The inclusion of any link does not imply endorsement by DARTS of the site. Use of any such linked website is at the user's own risk.</p>
      
      <h2>7. Modifications</h2>
      <p>DARTS may revise these terms of service for its website at any time without notice. By using this website, you are agreeing to be bound by the then current version of these terms of service.</p>
      
      <h2>8. Governing Law</h2>
      <p>These terms and conditions are governed by and construed in accordance with the laws of the jurisdiction in which DARTS operates, and you irrevocably submit to the exclusive jurisdiction of the courts in that location.</p>

    @elseif($page === 'documentation')
      <h1>Documentation</h1>
      <div class="updated">Last updated: {{ date('F j, Y') }}</div>
      
      <h2>1. Getting Started</h2>
      <h3>System Access</h3>
      <p>DARTS is a secure document management system designed for authorized personnel only. To gain access:</p>
      <ol>
        <li>Click "Request Access" on the login page</li>
        <li>Fill in your details including name, email, department, and desired role</li>
        <li>Submit your request for administrative review</li>
        <li>Wait for approval notification via email</li>
        <li>Log in with your credentials once approved</li>
      </ol>
      
      <h2>2. Dashboard Overview</h2>
      <p>Once logged in, you'll see the DARTS dashboard which includes:</p>
      <ul>
        <li><strong>My Requests:</strong> Track document requests you've submitted</li>
        <li><strong>Assigned:</strong> View documents assigned to you for processing</li>
        <li><strong>Archive:</strong> Access archived documents</li>
        <li><strong>Routing:</strong> Manage document routing workflows</li>
        <li><strong>Admin:</strong> Administration panel (for authorized users)</li>
      </ul>
      
      <h2>3. Creating a Document Request</h2>
      <p>To request a document:</p>
      <ol>
        <li>Navigate to "My Requests"</li>
        <li>Click "New Request"</li>
        <li>Fill in the document details and requirements</li>
        <li>Upload any supporting documents if needed</li>
        <li>Submit your request</li>
        <li>Track the status in your requests list</li>
      </ol>
      
      <h2>4. Managing Assigned Documents</h2>
      <p>Documents assigned to you for processing can be found in the "Assigned" section. Here you can:</p>
      <ul>
        <li>Review document details</li>
        <li>Update the status of documents</li>
        <li>Add comments and notes</li>
        <li>Transfer documents to other users</li>
        <li>Approve or reject requests</li>
      </ul>
      
      <h2>5. Archive Operations</h2>
      <p>Completed and processed documents are stored in the Archive. You can:</p>
      <ul>
        <li>Search and filter archived documents</li>
        <li>View document details</li>
        <li>Download documents</li>
        <li>View document history and metadata</li>
      </ul>
      
      <h2>6. Document Routing</h2>
      <p>Set up routing rules to automatically direct documents through workflows based on department, document type, and other criteria.</p>
      
      <h2>7. Support</h2>
      <p>For technical support or questions about using DARTS, please contact your system administrator or the IT department.</p>
    @endif

    <a href="/" class="back-link">← Back to Home</a>
  </div>
</body>
</html>
