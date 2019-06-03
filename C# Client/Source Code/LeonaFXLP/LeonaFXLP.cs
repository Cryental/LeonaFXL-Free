using System;
using System.Collections.Specialized;
using System.Diagnostics;
using System.Globalization;
using System.IO;
using System.Linq;
using System.Net;
using System.Net.Sockets;
using System.Security.Cryptography;
using System.Text;
using System.Windows.Forms;
using NUniqueHardwareID;

namespace NUniqueHardwareID
{
    internal static class CPURetriever
    {
        public static string GetCPUInfo()
        {
            var vendorIdentifier = Environment.GetEnvironmentVariable("Processor_Identifier");
            var revision = Environment.GetEnvironmentVariable("Processor_Revision");

            return vendorIdentifier + revision;
        }
    }

    internal static class HardDriveId
    {
        public static string GetSerialNumber()
        {
            var windowsDrive = Path.GetPathRoot(Environment.SystemDirectory);

            GetVolumeInformation(
                windowsDrive,
                null,
                0,
                out var serialNumber,
                out var maxComponentLength,
                out var sysFlags,
                null,
                0);

            return serialNumber.ToString();
        }

        [DllImport("Kernel32.dll", CharSet = CharSet.Auto, SetLastError = true)]
        [return: MarshalAs(UnmanagedType.Bool)]
        private static extern bool GetVolumeInformation(
            string rootPathName,
            StringBuilder volumeNameBuffer,
            int volumeNameSize,
            out uint volumeSerialNumber,
            out uint maximumComponentLength,
            out uint fileSystemFlags,
            StringBuilder fileSystemNameBuffer,
            int nFileSystemNameSize);
    }

    public interface IUniqueHardwareId
    {
        bool UseCPUInformation { get; set; }

        bool UseMACAddress { get; set; }

        bool UseVolumeInformation { get; set; }

        string CalculateHardwareId();

        string CalculateHardwareId(HashAlgorithm hashAlgorithm);
    }

    internal static class MACRetriever
    {
        public static string GetMACAddressFromPrimaryDevice()
        {
            if (!NetworkInterface.GetIsNetworkAvailable()) return null;

            var allNetworkInterfaces = NetworkInterface.GetAllNetworkInterfaces()
                .Where(i => i.NetworkInterfaceType != NetworkInterfaceType.Loopback)
                .ToList();

            return allNetworkInterfaces.Any()
                ? allNetworkInterfaces.First().GetPhysicalAddress().ToString()
                : null;
        }
    }

    internal class UniqueHardwareId : IUniqueHardwareId
    {
        public bool UseCPUInformation { get; set; } = true;

        public bool UseMACAddress { get; set; } = true;

        public bool UseVolumeInformation { get; set; } = true;

        public string CalculateHardwareId()
        {
            return CalculateHardwareId(MD5.Create());
        }

        public string CalculateHardwareId(HashAlgorithm hashAlgorithm)
        {
            if ((UseCPUInformation || UseMACAddress || UseVolumeInformation) == false) return null;

            var plainHardware = string.Empty;

            if (UseCPUInformation) plainHardware += CPURetriever.GetCPUInfo();

            if (UseMACAddress) plainHardware += MACRetriever.GetMACAddressFromPrimaryDevice();

            if (UseVolumeInformation) plainHardware += HardDriveId.GetSerialNumber();

            return GetHashString(plainHardware, hashAlgorithm);
        }

        private static string GetHashString(string inputString, HashAlgorithm hashAlgorithm)
        {
            var sb = new StringBuilder();
            foreach (var b in hashAlgorithm.ComputeHash(Encoding.UTF8.GetBytes(inputString)))
                sb.Append(b.ToString("X2"));

            return sb.ToString();
        }
    }
}

public class LeonaFXLP
{
    private static readonly Random random = new Random();
    private readonly string AccessKey = "leonafx_" + RandomString(24);
    
    //Base API Address
    private readonly string PublicURL = "https://example.com";
    
    //Import Values
    public string PublicURLExtension { get; set; }
    public string PrivateKey { get; set; }
    public byte[] PrivateIV { get; set; }
    public string CommunicationKey { get; set; }

    //Random String Generator
    public static string RandomString(int length)
    {
        const string chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        return new string(Enumerable.Repeat(chars, length)
            .Select(s => s[random.Next(s.Length)]).ToArray());
    }
    
    //Encrypt String Using AES
    public string EncryptString(string plainText, byte[] key, byte[] iv)
    {
        var encryptor = Aes.Create();

        encryptor.Mode = CipherMode.CBC;

        var aesKey = new byte[32];
        Array.Copy(key, 0, aesKey, 0, 32);
        encryptor.Key = aesKey;
        encryptor.IV = iv;

        var memoryStream = new MemoryStream();

        var aesEncryptor = encryptor.CreateEncryptor();

        var cryptoStream = new CryptoStream(memoryStream, aesEncryptor, CryptoStreamMode.Write);

        var plainBytes = Encoding.ASCII.GetBytes(plainText);

        cryptoStream.Write(plainBytes, 0, plainBytes.Length);

        cryptoStream.FlushFinalBlock();

        var cipherBytes = memoryStream.ToArray();

        memoryStream.Close();
        cryptoStream.Close();

        var cipherText = Convert.ToBase64String(cipherBytes, 0, cipherBytes.Length);

        return cipherText;
    }
    
    //Decrypt String Using AES
    public string DecryptString(string cipherText, byte[] key, byte[] iv)
    {
        var encryptor = Aes.Create();

        encryptor.Mode = CipherMode.CBC;

        var aesKey = new byte[32];
        Array.Copy(key, 0, aesKey, 0, 32);
        encryptor.Key = aesKey;
        encryptor.IV = iv;

        var memoryStream = new MemoryStream();

        var aesDecryptor = encryptor.CreateDecryptor();

        var cryptoStream = new CryptoStream(memoryStream, aesDecryptor, CryptoStreamMode.Write);

        var plainText = string.Empty;

        try
        {
            var cipherBytes = Convert.FromBase64String(cipherText);

            cryptoStream.Write(cipherBytes, 0, cipherBytes.Length);

            cryptoStream.FlushFinalBlock();

            var plainBytes = memoryStream.ToArray();

            plainText = Encoding.ASCII.GetString(plainBytes, 0, plainBytes.Length);
        }
        finally
        {
            memoryStream.Close();
            cryptoStream.Close();
        }

        return plainText;
    }

    //Get Time from NTP Server
    public static DateTime GetNetworkTime()
    {
        const string NtpServer = "time.google.com";

        const int DaysTo1900 = 1900 * 365 + 95;
        const long TicksPerSecond = 10000000L;
        const long TicksPerDay = 24 * 60 * 60 * TicksPerSecond;
        const long TicksTo1900 = DaysTo1900 * TicksPerDay;

        var ntpData = new byte[48];
        ntpData[0] = 0x1B;

        var addresses = Dns.GetHostEntry(NtpServer).AddressList;
        var ipEndPoint = new IPEndPoint(addresses[0], 123);
        var pingDuration = Stopwatch.GetTimestamp();
        using (var socket = new Socket(AddressFamily.InterNetwork, SocketType.Dgram, ProtocolType.Udp))
        {
            socket.Connect(ipEndPoint);
            socket.ReceiveTimeout = 5000;
            socket.Send(ntpData);
            pingDuration = Stopwatch.GetTimestamp();

            socket.Receive(ntpData);
            pingDuration = Stopwatch.GetTimestamp() - pingDuration;
        }

        var pingTicks = pingDuration * TicksPerSecond / Stopwatch.Frequency;

        var intPart = ((long) ntpData[40] << 24) | ((long) ntpData[41] << 16) | ((long) ntpData[42] << 8) | ntpData[43];
        var fractPart = ((long) ntpData[44] << 24) | ((long) ntpData[45] << 16) | ((long) ntpData[46] << 8) |
                        ntpData[47];
        var netTicks = intPart * TicksPerSecond + ((fractPart * TicksPerSecond) >> 32);

        var networkDateTime = new DateTime(TicksTo1900 + netTicks + pingTicks / 2);

        return networkDateTime;
    }
    
    //Timestamp Check, Need to merge to main module soon.
    private bool TimestampCheck(string timestamp)
    {
        if (string.IsNullOrEmpty(timestamp)) return false;

        ServicePointManager.SecurityProtocol = SecurityProtocolType.Tls12;
        try
        {
            var urlAddress = PublicURL + PublicURLExtension + "_timestamp";

            using (var _client = new ProtectedWebClient())
            {
                _client.Headers.Add("user-agent", AccessKey);
                var postData = new NameValueCollection
                {
                    {"timestamp", timestamp}
                };

                var received = Encoding.UTF8.GetString(_client.UploadValues(urlAddress, postData));
                var messagedecrypt = DecryptRJ256(PrivateKey, received);
                if (messagedecrypt == "TIMESTAMP_ERROR")
                    return false;
                return true;
            }
        }
        catch (Exception e)
        {
            MessageBox.Show(e.Message);
            return false;
        }
    }
    
    //Easy-To-Use Decryption
    private string DecryptRJ256(string prm_key, string prm_text_to_decrypt)
    {
        var mySHA256 = SHA256.Create();
        var key = mySHA256.ComputeHash(Encoding.ASCII.GetBytes(prm_key));
        return DecryptString(prm_text_to_decrypt, key, PrivateIV);
    }

    //Easy-To-Use Encryption
    private string EncryptRJ256(string prm_key, string prm_text_to_encrypt)
    {
        var mySHA256 = SHA256.Create();
        var key = mySHA256.ComputeHash(Encoding.ASCII.GetBytes(prm_key));
        var encrypted = EncryptString(prm_text_to_encrypt, key, PrivateIV);
        return encrypted;
    }
    
    //Hardware ID Generator
    private static string HardwareIDGenerate()
    {
        var hardwareIdGenerator = new UniqueHardwareId
        {
            UseVolumeInformation = true
        };

        return hardwareIdGenerator.CalculateHardwareId(SHA1.Create());
    }
    
    //Login Feature
    public string Login(string username, string password)
    {
        try
        {
            if (string.IsNullOrEmpty(username) || string.IsNullOrEmpty(password)) return "LOGIN_FIELDEMPTY";

            ServicePointManager.SecurityProtocol = SecurityProtocolType.Tls12;

            if (TimestampCheck(EncryptRJ256(PrivateKey,
                GetNetworkTime().ToString("yyyy-MM-dd hh:mm:ss", new CultureInfo("en-US")))))
            {
                var urlAddress = PublicURL + PublicURLExtension + "login";

                using (var _client = new ProtectedWebClient())
                {
                    _client.Headers.Add("user-agent", AccessKey);
                    var postData = new NameValueCollection
                    {
                        {"username", EncryptRJ256(PrivateKey, username)},
                        {"password", EncryptRJ256(PrivateKey, password)},
                        {"hwid", EncryptRJ256(PrivateKey, HardwareIDGenerate())},
                        {"c_key", EncryptRJ256(PrivateKey, CommunicationKey)}
                    };

                    var received = Encoding.UTF8.GetString(_client.UploadValues(urlAddress, postData));
                    var messagedecrypt = DecryptRJ256(PrivateKey, received);
                    var messageexport = messagedecrypt.Split('|');

                    return messageexport[1];
                }
            }
        }
        catch
        {
            return "UNKNOWN_ERROR";
        }

        return "UNKNOWN_ERROR";
    }

    //Register Feature
    public string Register(string username, string password, string rpassword, string email, string secret_qus)
    {
        try
        {
            if (string.IsNullOrEmpty(username) || string.IsNullOrEmpty(password) || string.IsNullOrEmpty(rpassword) ||
                string.IsNullOrEmpty(email) || string.IsNullOrEmpty(secret_qus)) return "REGISTER_FIELDEMPTY";

            ServicePointManager.SecurityProtocol = SecurityProtocolType.Tls12;

            if (TimestampCheck(EncryptRJ256(PrivateKey,
                GetNetworkTime().ToString("yyyy-MM-dd hh:mm:ss", new CultureInfo("en-US")))))
            {
                var urlAddress = PublicURL + PublicURLExtension + "register";

                using (var _client = new ProtectedWebClient())
                {
                    _client.Headers.Add("user-agent", AccessKey);
                    var postData = new NameValueCollection
                    {
                        {"username", EncryptRJ256(PrivateKey, username)},
                        {"password", EncryptRJ256(PrivateKey, password)},
                        {"rpassword", EncryptRJ256(PrivateKey, rpassword)},
                        {"email", EncryptRJ256(PrivateKey, email)},
                        {"hwid", EncryptRJ256(PrivateKey, HardwareIDGenerate())},
                        {"secret_qus", EncryptRJ256(PrivateKey, secret_qus)},
                        {"c_key", EncryptRJ256(PrivateKey, CommunicationKey)}
                    };

                    var received = Encoding.UTF8.GetString(_client.UploadValues(urlAddress, postData));
                    var messagedecrypt = DecryptRJ256(PrivateKey, received);
                    var messageexport = messagedecrypt.Split('|');

                    return messageexport[1];
                }
            }
        }
        catch
        {
            return "UNKNOWN_ERROR";
        }

        return "UNKNOWN_ERROR";
    }
    
    //Autoban Feature, you can delete it if you don't need this feature.
    public string AutoBan(string username)
    {
        try
        {
            if (string.IsNullOrEmpty(username)) return "AUTOBAN_FIELDEMPTY";

            if (TimestampCheck(EncryptRJ256(PrivateKey,
                GetNetworkTime().ToString("yyyy-MM-dd hh:mm:ss", new CultureInfo("en-US")))))
            {
                ServicePointManager.SecurityProtocol = SecurityProtocolType.Tls12;

                var urlAddress = PublicURL + PublicURLExtension + "autoban";

                using (var _client = new ProtectedWebClient())
                {
                    _client.Headers.Add("user-agent", AccessKey);
                    var postData = new NameValueCollection
                    {
                        {"username", EncryptRJ256(PrivateKey, username)},
                        {"hwid", EncryptRJ256(PrivateKey, HardwareIDGenerate())}
                    };

                    var received = Encoding.UTF8.GetString(_client.UploadValues(urlAddress, postData));
                    var messagedecrypt = DecryptRJ256(PrivateKey, received);
                    var messageexport = messagedecrypt.Split('|');

                    return messageexport[1];
                }
            }
        }
        catch
        {
            return "UNKNOWN_ERROR";
        }

        return "UNKNOWN_ERROR";
    }

    //Change Password Feature
    public string ChangePassword(string username, string oldpassword, string newpassword, string rnewpassword)
    {
        try
        {
            if (string.IsNullOrEmpty(username) || string.IsNullOrEmpty(oldpassword) ||
                string.IsNullOrEmpty(newpassword) || string.IsNullOrEmpty(rnewpassword)) return "CHANGEPWD_FIELDEMPTY";

            if (TimestampCheck(EncryptRJ256(PrivateKey,
                GetNetworkTime().ToString("yyyy-MM-dd hh:mm:ss", new CultureInfo("en-US")))))
            {
                ServicePointManager.SecurityProtocol = SecurityProtocolType.Tls12;

                var urlAddress = PublicURL + PublicURLExtension + "change_pwd";

                using (var _client = new ProtectedWebClient())
                {
                    _client.Headers.Add("user-agent", AccessKey);
                    var postData = new NameValueCollection
                    {
                        {"username", EncryptRJ256(PrivateKey, username)},
                        {"oldpassword", EncryptRJ256(PrivateKey, oldpassword)},
                        {"newpassword", EncryptRJ256(PrivateKey, newpassword)},
                        {"rnewpassword", EncryptRJ256(PrivateKey, rnewpassword)},
                        {"hwid", EncryptRJ256(PrivateKey, HardwareIDGenerate())}
                    };

                    var received = Encoding.UTF8.GetString(_client.UploadValues(urlAddress, postData));
                    var messagedecrypt = DecryptRJ256(PrivateKey, received);
                    var messageexport = messagedecrypt.Split('|');

                    return messageexport[1];
                }
            }
        }
        catch
        {
            return "UNKNOWN_ERROR";
        }

        return "UNKNOWN_ERROR";
    }
    
    //Restore Password Feature
    public string ForgotPassword(string username, string email, string secret_quest, string newpassword,
        string rnewpassword)
    {
        try
        {
            if (string.IsNullOrEmpty(username) || string.IsNullOrEmpty(email) || string.IsNullOrEmpty(secret_quest) ||
                string.IsNullOrEmpty(newpassword) || string.IsNullOrEmpty(rnewpassword)) return "FORGOTPWD_FIELDEMPTY";

            if (TimestampCheck(EncryptRJ256(PrivateKey,
                GetNetworkTime().ToString("yyyy-MM-dd hh:mm:ss", new CultureInfo("en-US")))))
            {
                ServicePointManager.SecurityProtocol = SecurityProtocolType.Tls12;

                var urlAddress = PublicURL + PublicURLExtension + "forgot_pwd";

                using (var _client = new ProtectedWebClient())
                {
                    _client.Headers.Add("user-agent", AccessKey);
                    var postData = new NameValueCollection
                    {
                        {"username", EncryptRJ256(PrivateKey, username)},
                        {"email", EncryptRJ256(PrivateKey, email)},
                        {"secret_quest", EncryptRJ256(PrivateKey, secret_quest)},
                        {"newpassword", EncryptRJ256(PrivateKey, newpassword)},
                        {"rnewpassword", EncryptRJ256(PrivateKey, rnewpassword)},
                        {"hwid", EncryptRJ256(PrivateKey, HardwareIDGenerate())}
                    };

                    var received = Encoding.UTF8.GetString(_client.UploadValues(urlAddress, postData));
                    var messagedecrypt = DecryptRJ256(PrivateKey, received);
                    var messageexport = messagedecrypt.Split('|');

                    return messageexport[1];
                }
            }
        }
        catch
        {
            return "UNKNOWN_ERROR";
        }

        return "UNKNOWN_ERROR";
    }
    
    //License Register Feature
    public string LicenseRegister(string username, string password, string timeleft, bool lifetime)
    {
        try
        {
            if (string.IsNullOrEmpty(username) || string.IsNullOrEmpty(password) || string.IsNullOrEmpty(timeleft))
                return "LICENSEREG_FIELDEMPTY";

            if (TimestampCheck(EncryptRJ256(PrivateKey,
                GetNetworkTime().ToString("yyyy-MM-dd hh:mm:ss", new CultureInfo("en-US")))))
            {
                ServicePointManager.SecurityProtocol = SecurityProtocolType.Tls12;

                var urlAddress = PublicURL + PublicURLExtension + "license_reg";

                using (var _client = new ProtectedWebClient())
                {
                    _client.Headers.Add("user-agent", AccessKey);
                    var postData = new NameValueCollection
                    {
                        {"username", EncryptRJ256(PrivateKey, username)},
                        {"password", EncryptRJ256(PrivateKey, password)},
                        {"hwid", EncryptRJ256(PrivateKey, HardwareIDGenerate())},
                        {"timeleft", EncryptRJ256(PrivateKey, timeleft)},
                        {"lifetime", EncryptRJ256(PrivateKey, Convert.ToInt32(lifetime).ToString())}
                    };

                    var received = Encoding.UTF8.GetString(_client.UploadValues(urlAddress, postData));
                    var messagedecrypt = DecryptRJ256(PrivateKey, received);
                    var messageexport = messagedecrypt.Split('|');

                    return messageexport[1];
                }
            }
        }
        catch
        {
            return "UNKNOWN_ERROR";
        }

        return "UNKNOWN_ERROR";
    }
    public string LicenseVaildate(string username, string password, string license)
    {
        try
        {
            if (string.IsNullOrEmpty(username) || string.IsNullOrEmpty(password) || string.IsNullOrEmpty(license))
                return "LICENSEVAILD_FIELDEMPTY";

            if (TimestampCheck(EncryptRJ256(PrivateKey,
                GetNetworkTime().ToString("yyyy-MM-dd hh:mm:ss", new CultureInfo("en-US")))))
            {
                ServicePointManager.SecurityProtocol = SecurityProtocolType.Tls12;

                var urlAddress = PublicURL + PublicURLExtension + "license_vaild";

                using (var _client = new ProtectedWebClient())
                {
                    _client.Headers.Add("user-agent", AccessKey);
                    var postData = new NameValueCollection
                    {
                        {"username", EncryptRJ256(PrivateKey, username)},
                        {"password", EncryptRJ256(PrivateKey, password)},
                        {"hwid", EncryptRJ256(PrivateKey, HardwareIDGenerate())},
                        {"license", EncryptRJ256(PrivateKey, license)}
                    };

                    var received = Encoding.UTF8.GetString(_client.UploadValues(urlAddress, postData));
                    var messagedecrypt = DecryptRJ256(PrivateKey, received);
                    var messageexport = messagedecrypt.Split('|');

                    return messageexport[1];
                }
            }
        }
        catch
        {
            return "UNKNOWN_ERROR";
        }

        return "UNKNOWN_ERROR";
    }
}

//SSL Vaildation for Man-in-middle attack, you should to use HTTPS for your server.
internal class ProtectedWebClient : WebClient
{
    public ProtectedWebClient()
    {
        Validate = true;
    }

    public bool Validate { get; set; }

    protected override WebRequest GetWebRequest(Uri url)
    {
        var _req = (HttpWebRequest) base.GetWebRequest(url);

        if (Validate)
            _req.ServerCertificateValidationCallback = (s, cert, chain, polErr) =>
            {
                if ((cert.Subject.Contains(".cloudflaressl.com") || cert.Subject.Contains("example.com")) &
                    (cert.Issuer.Contains("CloudFlare") || cert.Issuer.Contains("RapidSSL")) //Change values following your certificate.
                    return true;
                return false;
            };
        return _req;
    }
}
