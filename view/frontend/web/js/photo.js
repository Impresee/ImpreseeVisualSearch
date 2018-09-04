//draw the image on canvas and rotate it
/**
 * To preprocess an image before send it to Impresee search services
 * @param DOMstring with data uri
 * @param int with orientation of image
 */
function sendPhoto(photo, orientation)
{
  var reader = new FileReader();
  reader.onloadend = function () {
    var tempImg = new Image();
    tempImg.src = reader.result;
    tempImg.onload = function (e) {
      var MAX_WIDTH = 640;
      var MAX_HEIGHT = 480;
      var tempW = tempImg.width;
      var tempH = tempImg.height;
      if (tempW > tempH) {
        if (tempW > MAX_WIDTH) {
          tempH *= MAX_WIDTH / tempW;
          tempW = MAX_WIDTH;
        }
      } else {
        if (tempH > MAX_HEIGHT) {
          tempW *= MAX_HEIGHT / tempH;
          tempH = MAX_HEIGHT;
        }
      }
      var canvas = document.createElement('canvas');
      var ctx = canvas.getContext("2d");
      switch (orientation) {
        case 6:
          canvas.height = tempW;
          canvas.width = tempH;
          // move to the center of the canvas
          ctx.translate(canvas.width / 2, canvas.height / 2);
          // rotate the canvas to the specified degrees
          ctx.rotate(90 * Math.PI / 180);
          // draw the image
          // since the ctx is rotated, the image will be rotated also
          ctx.drawImage(this, -canvas.height / 2, -canvas.width / 2, canvas.height, canvas.width);
          break;
        case 7:
          canvas.height = tempW;
          canvas.width = tempH;
          // move to the center of the canvas
          ctx.translate(canvas.width / 2, canvas.height / 2);
          // rotate the canvas to the specified degrees
          ctx.rotate(270 * Math.PI / 180);
          // draw the image
          // since the ctx is rotated, the image will be rotated also
          ctx.drawImage(this, -canvas.height / 2, -canvas.width / 2, canvas.height, canvas.width);
          break;
        default:
          canvas.width = tempW;
          canvas.height = tempH;
          var ctx = canvas.getContext("2d");
          ctx.drawImage(this, 0, 0, tempW, tempH);
      }
      var dataURL = canvas.toDataURL("image/jpeg");
      makeSearch("photo", dataURL);
    };
  };
  reader.readAsDataURL(photo);
}
/**
 * Get the image orientation and send photo to preprocess on callback
 * @param file
 */
function takeImageFile(file)
{
  getOrientation(file, function (orientation) {
    sendPhoto(file, orientation);
  });
}
/**
 * Get te orientation flag from exif metadata
 * -2 not jpg
 * -1 not defined
 * @param file image
 * @param function callback
 */
function getOrientation(file, callback)
{
  var reader = new FileReader();
  reader.onload = function (e) {
    var view = new DataView(e.target.result);
    if (view.getUint16(0, false) != 0xFFD8) {
return callback(file,-2); }
    var length = view.byteLength,
      offset = 2;
    while (offset < length) {
      var marker = view.getUint16(offset, false);
      offset += 2;
      if (marker == 0xFFE1) {
        if (view.getUint32(offset += 2, false) != 0x45786966) {
return callback(file, -1); }
        var little = view.getUint16(offset += 6, false) == 0x4949;
        offset += view.getUint32(offset + 4, little);
        var tags = view.getUint16(offset, little);
        offset += 2;
        for (var i = 0; i < tags; i++) {
          if (view.getUint16(offset + (i * 12), little) == 0x0112) {
            return callback(file, view.getUint16(offset + (i * 12) + 8, little)); } }
      } else if ((marker & 0xFF00) != 0xFF00) {
break; } else {
offset += view.getUint16(offset, false); }
    }
    return callback(file, -1);
  };
  reader.readAsArrayBuffer(file.files[0]);
}
/**
 * To resize canvas
 * @param context of canvas
 */
function reduceCanvas(content)
{
  var tempImg = new Image();
  tempImg.src = content;
  tempImg.onload = function (e) {
    var MAX_WIDTH = 640;
    var MAX_HEIGHT = 480;
    var tempW = tempImg.width;
    var tempH = tempImg.height;
    if (tempW > tempH) {
      if (tempW > MAX_WIDTH) {
        tempH *= MAX_WIDTH / tempW;
        tempW = MAX_WIDTH;
      }
    } else {
      if (tempH > MAX_HEIGHT) {
        tempW *= MAX_HEIGHT / tempH;
        tempH = MAX_HEIGHT;
      }
    }
    var canvas = document.createElement('canvas');
    canvas.width = tempW;
    canvas.height = tempH;
    var ctx = canvas.getContext("2d");
    ctx.drawImage(this, 0, 0, tempW, tempH);
    var dataURL = canvas.toDataURL("image/jpeg");
    makeSearch("photo",dataURL);
  }
}
