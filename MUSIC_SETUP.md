# Background Music Setup Guide

## Overview

The website now includes a background music player that appears as a floating control panel in the bottom-right corner of the page. The player features play/pause controls, volume adjustment, and mute functionality.

## Features

- **Floating Music Player**: Fixed position in bottom-right corner
- **Play/Pause Control**: Click to start or stop music
- **Volume Slider**: Adjust volume from 0-100%
- **Mute Button**: Quick mute/unmute functionality
- **Auto-loop**: Music automatically repeats
- **Responsive Design**: Works on desktop and mobile devices
- **Theme Integration**: Adapts to light/dark themes

## Setup Instructions

### 1. Create Music Directory

In your DirectAdmin File Manager:

1. Navigate to `public_html/`
2. Create a new folder called `music`
3. The full path should be: `public_html/music/`

### 2. Upload Music File

1. Upload your MP3 file to the `music` folder
2. Rename the file to `background-music.mp3`
3. The final path should be: `public_html/music/background-music.mp3`

### 3. File Requirements

- **Format**: MP3 (recommended) or other web-compatible audio formats
- **File Size**: Keep under 5MB for faster loading
- **Duration**: 2-5 minutes recommended for looping
- **Quality**: 128-192 kbps is sufficient for background music

### 4. Customize Music Title

To change the displayed song title:

1. Open `index.html` in a text editor
2. Find this line: `<span id="song-title">Background Music</span>`
3. Replace "Background Music" with your song title
4. Save and upload the file

## Technical Details

### Browser Compatibility

The music player works with all modern browsers:
- Chrome, Firefox, Safari, Edge
- Mobile browsers (iOS Safari, Android Chrome)

### Browser Policies

Due to browser autoplay policies:
- Music will not start automatically
- User must click somewhere on the page first
- This ensures compliance with browser requirements

### Audio Element

The player uses HTML5 `<audio>` element:
```html
<audio id="background-music" loop>
    <source src="music/background-music.mp3" type="audio/mpeg">
    Your browser does not support the audio element.
</audio>
```

## Customization Options

### Change Music File Path

If you want to use a different filename:

1. Edit `index.html`
2. Find: `src="music/background-music.mp3"`
3. Change to your filename: `src="music/your-song.mp3"`

### Adjust Player Position

To move the player to a different corner:

1. Edit the CSS in `index.html`
2. Find `.music-player` style
3. Change `bottom: 20px; right: 20px;` to:
   - Top-left: `top: 20px; left: 20px;`
   - Top-right: `top: 20px; right: 20px;`
   - Bottom-left: `bottom: 20px; left: 20px;`

### Change Player Colors

To customize the player appearance:

1. Edit the CSS in `index.html`
2. Find `.music-player` style
3. Modify `background: rgba(52, 152, 219, 0.9);` to your preferred color

## Troubleshooting

### Music Not Playing

1. Check file path: `public_html/music/background-music.mp3`
2. Verify file format is MP3
3. Check browser console for errors
4. Ensure file permissions allow web access

### Player Not Visible

1. Check if CSS is loaded properly
2. Verify z-index value (should be 1000)
3. Check for JavaScript errors in browser console

### Volume Issues

1. Check browser volume settings
2. Verify system volume is not muted
3. Test with different browsers

## Adding to Other Pages

To add the music player to other pages:

1. Copy the HTML structure from `index.html`
2. Copy the CSS styles
3. Copy the JavaScript functionality
4. Paste into the desired page before the closing `</body>` tag

## Performance Considerations

- Keep music file size small (under 5MB)
- Use compressed audio formats
- Consider loading music only on main pages
- Test on slower connections

## Legal Considerations

- Ensure you have rights to use the music
- Consider royalty-free music for commercial use
- Provide option to disable music for accessibility
- Follow copyright laws in your jurisdiction 