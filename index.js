const discord = require('discord.js');
var axios = require('axios');
const config = require('./config.json');
const client = new discord.Client();


client.on('ready', () => {
    console.log(`Bot is ready - ${client.user.tag}!`);
});


client.on('message', async (msg) => {
  if (msg.content.startsWith(`${config.prefix}verify`)) {

    if (msg.channel.id !== config.ChannelVerificationID){
        msg.channel.send("You can't use that command in this chat");
    }
    else{
        axios.get(`/MyProjects/DiscordVerifyPHP-JS/Database/Users/${msg.member.id}.json`)
        .then(response => {    
            if(!msg.member.roles.cache.has(config.VerifiedRoleID)){
                JSON.parse(JSON.stringify(response.data), (key, value) => {
                    if(key == 'Verified' && value == true){
                        msg.member.roles.add(config.VerifiedRoleID).catch(console.error);
                        const verifiedmsg = new discord.MessageEmbed()
                            .setTitle(`Verification - Developed by MRX450`)
                            .setColor("#333")
                            .setDescription(`You have been successfully verified :white_check_mark:`)
                            .setTimestamp()
                            .setFooter(`${msg.member.user.tag}`, `${msg.member.user.displayAvatarURL()}`);
                        msg.channel.send(verifiedmsg);
                    }
                });
            }
            else if(msg.member.roles.cache.has(config.VerifiedRoleID)){
                const verifiedhasrole = new discord.MessageEmbed()
                    .setTitle(`Verification - Developed by MRX450`)
                    .setColor("#333")
                    .setDescription(`You have already been verified, lol <@${msg.member.id}> :woman_facepalming: :man_facepalming: `)
                    .setTimestamp()
                    .setFooter(`${msg.member.user.tag}`, `${msg.member.user.displayAvatarURL()}`);
                msg.channel.send(verifiedhasrole);
            }
        })
        .catch((error) => {
            if(error.response.status == 404) {
                //console.log(`Error ${error.response.status} - Page Not Found`); //console.log("Error 404 - Page Not Found");
                const unverifiedmsg = new discord.MessageEmbed()
                    .setTitle(`------Verification------`)
                    .setColor("#333")
                    .setDescription(`**To be verified** \n [Click here](http://localhost/MyProjects/DiscordVerifyPHP-JS/verify.php)`)
                    .setTimestamp()
                    .setFooter(`${msg.member.user.tag}`, `${msg.member.user.displayAvatarURL()}`);
                msg.channel.send(unverifiedmsg);
            }
            else{
                //console.log("Somethings is wrong");
                msg.channel.send("Somethings is wrong");
            }
        });
    }
  }
});
client.login(config.token); 