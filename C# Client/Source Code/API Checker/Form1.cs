using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Drawing;
using System.Text;
using System.Threading.Tasks;
using System.Windows.Forms;

namespace API_Checker
{
    public partial class Form1 : Form
    {
        public Form1()
        {
            InitializeComponent();
        }

        private void groupBox6_Enter(object sender, EventArgs e)
        {

        }

        private byte[] tempiv = new byte[16] { 0x0, 0x0, 0x0, 0x0, 0x0, 0x0, 0x0, 0x0, 0x0, 0x0, 0x0, 0x0, 0x0, 0x0, 0x0, 0x0 };

        private void button1_Click(object sender, EventArgs e)
        {
            var LeonaFXLP = new global::LeonaFXLP();
            LeonaFXLP.PrivateKey = textBox1.Text;
            LeonaFXLP.PrivateIV = tempiv;
            LeonaFXLP.CommunicationKey = "jRu44GFdyhFPCPq9C2Wf";

            textBox6.Text = LeonaFXLP.Login(textBox5.Text, textBox4.Text);
        }

        private void button2_Click(object sender, EventArgs e)
        {
            var LeonaFXLP = new global::LeonaFXLP();
            LeonaFXLP.PrivateKey = textBox1.Text;
            LeonaFXLP.PrivateIV = tempiv;
            LeonaFXLP.CommunicationKey = "jRu44GFdyhFPCPq9C2Wf";

            textBox6.Text = LeonaFXLP.Register(textBox8.Text, textBox7.Text, textBox10.Text, textBox9.Text, textBox11.Text);
        }

        private void button4_Click(object sender, EventArgs e)
        {
            var LeonaFXLP = new global::LeonaFXLP();
            LeonaFXLP.PrivateKey = textBox1.Text;
            LeonaFXLP.PrivateIV = tempiv;
            LeonaFXLP.CommunicationKey = "jRu44GFdyhFPCPq9C2Wf";

            textBox6.Text = LeonaFXLP.ChangePassword(textBox23.Text, textBox22.Text, textBox21.Text, textBox20.Text);
        }

        private void button3_Click(object sender, EventArgs e)
        {
            var LeonaFXLP = new global::LeonaFXLP();
            LeonaFXLP.PrivateKey = textBox1.Text;
            LeonaFXLP.PrivateIV = tempiv;
            LeonaFXLP.CommunicationKey = "jRu44GFdyhFPCPq9C2Wf";

            textBox6.Text = LeonaFXLP.ForgotPassword(textBox16.Text, textBox15.Text, textBox13.Text, textBox12.Text, textBox17.Text);
        }

        private void button5_Click(object sender, EventArgs e)
        {
            var LeonaFXLP = new global::LeonaFXLP();
            LeonaFXLP.PrivateKey = textBox1.Text;
            LeonaFXLP.PrivateIV = tempiv;
            LeonaFXLP.CommunicationKey = "jRu44GFdyhFPCPq9C2Wf";

            textBox6.Text = LeonaFXLP.LicenseRegister(textBox25.Text, textBox24.Text, textBox19.Text, checkBox1.Checked);
        }

        private void button8_Click(object sender, EventArgs e)
        {
            var LeonaFXLP = new global::LeonaFXLP();
            LeonaFXLP.PrivateKey = textBox1.Text;
            LeonaFXLP.PrivateIV = tempiv;
            LeonaFXLP.CommunicationKey = "jRu44GFdyhFPCPq9C2Wf";

            textBox6.Text = LeonaFXLP.LicenseVaildate(textBox33.Text, textBox32.Text, textBox31.Text);
        }

        private void button9_Click(object sender, EventArgs e)
        {
            var LeonaFXLP = new global::LeonaFXLP();
            LeonaFXLP.PrivateKey = textBox1.Text;
            LeonaFXLP.PrivateIV = tempiv;
            LeonaFXLP.CommunicationKey = "jRu44GFdyhFPCPq9C2Wf";

            textBox6.Text = LeonaFXLP.Test();
        }
    }
}
